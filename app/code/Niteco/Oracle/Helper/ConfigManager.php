<?php
/**
 * Niteco
 * User: tinh.ngo
 * Date: 3/1/2019
 * Time: 2:21 PM
 */

namespace Niteco\Oracle\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class ConfigManager extends AbstractHelper {

    protected $scopeConfig;
    private $sentEmailLogger;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Niteco\Oracle\Common\SentEmailLogger $sentEmailLogger
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->sentEmailLogger = $sentEmailLogger;

        parent::__construct($context);
    }

    public function getRedisHost() {
        return $this->getConfig('general', 'redis_host');
    }

    public function getRedisPort() {
        return $this->getConfig('general', 'redis_port');
    }

    public function getRedisPassword() {
        return $this->getConfig('general', 'redis_password');
    }

    public function getRedisQueueKey() {
        return $this->getConfig('general', 'queue_key');
    }

    public function getRedisStoreId() {
        return $this->getConfig('general', 'store_id');
    }

    public function getOracleEndpoint() {
        return $this->getConfig('general', 'oracle_endpoint');
    }

    public function getOracleAuth() {
        return $this->getConfig('general', 'oracle_auth');
    }

    public function isSendOrderEnable() {
        return $this->getConfig('general', 'enabled');
    }

    public function getApiTimeout() {
        return $this->getConfig('general', 'api_timeout');
    }

    public function getEmailReceive() {
        return $this->getConfig('send_mail', 'email_receive');
    }

    public function isSendMailEnable() {
        return $this->getConfig('send_mail', 'enabled');
    }


    /**
     * Fetch configuration
     *
     * @param $tab
     * @param $field
     *
     * @return mixed
     */
    public function getConfig($tab, $field)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        return  $this->scopeConfig->getValue('niteco_oracle/'.$tab.'/'.$field, $storeScope);

    }

    public function getConfigCurrentStore($tab, $field, $storeId)
    {
        return $this->scopeConfig->getValue('niteco_oracle/' . $tab . '/' . $field,\Magento\Store\Model\ScopeInterface::SCOPE_STORES, $storeId);
    }

    public function getGeneralEmail() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue('trans_email/ident_general/email', $storeScope);
    }

    public function getGeneralName() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue('trans_email/ident_general/name', $storeScope);
    }

}