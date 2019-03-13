<?php
/**
 * Niteco
 * User: tinh.ngo
 * Date: 2/20/2019
 * Time: 3:43 PM
 */

namespace Niteco\Oracle\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();


        // Create new table
        $table = $setup->getConnection()->newTable(
            $setup->getTable('niteco_oracle_schedule')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Order id'
        )->addColumn(
            'increment_id',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Order increment id'
        )->addColumn(
            'status',
            Table::TYPE_INTEGER,
            10,
            ['nullable' => false, 'default' => '0'],
            'Status oracle'
        )->addColumn(
            'message',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Message oracle'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true, 'default' => Table::TIMESTAMP_INIT],
            'Order create at'
        )->addColumn(
            'executed_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true],
            'Execute schedule'
        )->addColumn(
            'finished_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true],
            'Finished schedule'
        )->addIndex(
            $setup->getIdxName('niteco_oracle_schedule', ['status']),
            ['status']
        )->addIndex(
            $setup->getIdxName(
                'niteco_oracle_schedule',
                ['entity_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['entity_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        );

        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
