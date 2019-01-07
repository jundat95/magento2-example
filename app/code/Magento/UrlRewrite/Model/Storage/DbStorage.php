<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\UrlRewrite\Model\Storage;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\UrlRewrite\Model\OptionProvider;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Url rewrites DB storage.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DbStorage extends AbstractStorage
{
    /**
     * DB Storage table name
     */
    const TABLE_NAME = 'url_rewrite';

    /**
     * Code of "Integrity constraint violation: 1062 Duplicate entry" error
     */
    const ERROR_CODE_DUPLICATE_ENTRY = 1062;

    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var Resource
     */
    protected $resource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ResourceConnection $resource
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        UrlRewriteFactory $urlRewriteFactory,
        DataObjectHelper $dataObjectHelper,
        ResourceConnection $resource,
        LoggerInterface $logger = null
    ) {
        $this->connection = $resource->getConnection();
        $this->resource = $resource;
        $this->logger = $logger ?: ObjectManager::getInstance()
            ->get(LoggerInterface::class);

        parent::__construct($urlRewriteFactory, $dataObjectHelper);
    }

    /**
     * Prepare select statement for specific filter
     *
     * @param array $data
     * @return Select
     */
    protected function prepareSelect(array $data)
    {
        $select = $this->connection->select();
        $select->from($this->resource->getTableName(self::TABLE_NAME));

        foreach ($data as $column => $value) {
            $select->where($this->connection->quoteIdentifier($column) . ' IN (?)', $value);
        }

        return $select;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFindAllByData(array $data)
    {
        return $this->connection->fetchAll($this->prepareSelect($data));
    }

    /**
     * {@inheritdoc}
     */
    protected function doFindOneByData(array $data)
    {
        if (array_key_exists(UrlRewrite::REQUEST_PATH, $data)
            && is_string($data[UrlRewrite::REQUEST_PATH])
        ) {
            $result = null;

            $requestPath = $data[UrlRewrite::REQUEST_PATH];

            $data[UrlRewrite::REQUEST_PATH] = [
                rtrim($requestPath, '/'),
                rtrim($requestPath, '/') . '/',
            ];

            $resultsFromDb = $this->connection->fetchAll($this->prepareSelect($data));

            if (count($resultsFromDb) === 1) {
                $resultFromDb = current($resultsFromDb);
                $redirectTypes = [OptionProvider::TEMPORARY, OptionProvider::PERMANENT];

                // If request path matches the DB value or it's redirect - we can return result from DB
                $canReturnResultFromDb = ($resultFromDb[UrlRewrite::REQUEST_PATH] === $requestPath
                    || in_array((int)$resultFromDb[UrlRewrite::REDIRECT_TYPE], $redirectTypes, true));

                // Otherwise return 301 redirect to request path from DB results
                $result = $canReturnResultFromDb ? $resultFromDb : [
                    UrlRewrite::ENTITY_TYPE => 'custom',
                    UrlRewrite::ENTITY_ID => '0',
                    UrlRewrite::REQUEST_PATH => $requestPath,
                    UrlRewrite::TARGET_PATH => $resultFromDb[UrlRewrite::REQUEST_PATH],
                    UrlRewrite::REDIRECT_TYPE => OptionProvider::PERMANENT,
                    UrlRewrite::STORE_ID => $resultFromDb[UrlRewrite::STORE_ID],
                    UrlRewrite::DESCRIPTION => null,
                    UrlRewrite::IS_AUTOGENERATED => '0',
                    UrlRewrite::METADATA => null,
                ];
            } else {
                // If we have 2 results - return the row that matches request path
                foreach ($resultsFromDb as $resultFromDb) {
                    if ($resultFromDb[UrlRewrite::REQUEST_PATH] === $requestPath) {
                        $result = $resultFromDb;
                        break;
                    }
                }
            }

            return $result;
        }

        return $this->connection->fetchRow($this->prepareSelect($data));
    }

    /**
     * Delete old URLs from DB.
     *
     * @param UrlRewrite[] $urls
     * @return void
     */
    private function deleteOldUrls(array $urls): void
    {
        $oldUrlsSelect = $this->connection->select();
        $oldUrlsSelect->from(
            $this->resource->getTableName(self::TABLE_NAME)
        );
        /** @var UrlRewrite $url */
        foreach ($urls as $url) {
            $oldUrlsSelect->orWhere(
                $this->connection->quoteIdentifier(
                    UrlRewrite::ENTITY_TYPE
                ) . ' = ?',
                $url->getEntityType()
            );
            $oldUrlsSelect->where(
                $this->connection->quoteIdentifier(
                    UrlRewrite::ENTITY_ID
                ) . ' = ?',
                $url->getEntityId()
            );
            $oldUrlsSelect->where(
                $this->connection->quoteIdentifier(
                    UrlRewrite::STORE_ID
                ) . ' = ?',
                $url->getStoreId()
            );
        }

        // prevent query locking in a case when nothing to delete
        $checkOldUrlsSelect = clone $oldUrlsSelect;
        $checkOldUrlsSelect->reset(Select::COLUMNS);
        $checkOldUrlsSelect->columns('count(*)');
        $hasOldUrls = (bool)$this->connection->fetchOne($checkOldUrlsSelect);

        if ($hasOldUrls) {
            $this->connection->query(
                $oldUrlsSelect->deleteFromSelect(
                    $this->resource->getTableName(self::TABLE_NAME)
                )
            );
        }
    }

    /**
     * @inheritDoc
     */
    protected function doReplace(array $urls)
    {
        $this->deleteOldUrls($urls);

        $data = [];
        foreach ($urls as $url) {
            $data[] = $url->toArray();
        }
        try {
            $this->insertMultiple($data);
        } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
            /** @var \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[] $urlConflicted */
            $urlConflicted = [];
            foreach ($urls as $url) {
                $urlFound = $this->doFindOneByData(
                    [
                        UrlRewrite::REQUEST_PATH => $url->getRequestPath(),
                        UrlRewrite::STORE_ID => $url->getStoreId(),
                    ]
                );
                if (isset($urlFound[UrlRewrite::URL_REWRITE_ID])) {
                    $urlConflicted[$urlFound[UrlRewrite::URL_REWRITE_ID]] = $url->toArray();
                }
            }
            if ($urlConflicted) {
                throw new \Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException(
                    __('URL key for specified store already exists.'),
                    $e,
                    $e->getCode(),
                    $urlConflicted
                );
            } else {
                throw $e->getPrevious() ?: $e;
            }
        }

        return $urls;
    }

    /**
     * Insert multiple
     *
     * @param array $data
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException|\Exception
     * @throws \Exception
     */
    protected function insertMultiple($data)
    {
        try {
            $this->connection->insertMultiple($this->resource->getTableName(self::TABLE_NAME), $data);
        } catch (\Exception $e) {
            if (($e->getCode() === self::ERROR_CODE_DUPLICATE_ENTRY)
                && preg_match('#SQLSTATE\[23000\]: [^:]+: 1062[^\d]#', $e->getMessage())
            ) {
                throw new \Magento\Framework\Exception\AlreadyExistsException(
                    __('URL key for specified store already exists.'),
                    $e
                );
            }
            throw $e;
        }
    }

    /**
     * Get filter for url rows deletion due to provided urls
     *
     * @param UrlRewrite[] $urls
     * @return array
     * @deprecated 101.0.3 Not used anymore.
     */
    protected function createFilterDataBasedOnUrls($urls)
    {
        $data = [];
        foreach ($urls as $url) {
            $entityType = $url->getEntityType();
            foreach ([UrlRewrite::ENTITY_ID, UrlRewrite::STORE_ID] as $key) {
                $fieldValue = $url->getByKey($key);
                if (!isset($data[$entityType][$key]) || !in_array($fieldValue, $data[$entityType][$key])) {
                    $data[$entityType][$key][] = $fieldValue;
                }
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByData(array $data)
    {
        $this->connection->query(
            $this->prepareSelect($data)->deleteFromSelect($this->resource->getTableName(self::TABLE_NAME))
        );
    }
}
