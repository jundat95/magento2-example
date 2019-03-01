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

    protected  $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    public function getEmailReceive() {
        $emailReceive = $this->scopeConfig('send_mail', 'niteco_oracle_email_receive');
        return $emailReceive;
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
        return  $this->_scopeConfig->getValue('niteco_oracle/'.$tab.'/'.$field, $storeScope);

    }

    public function getConfigCurrentStore($tab, $field, $storeId)
    {
        return $this->_scopeConfig->getValue('niteco_oracle/' . $tab . '/' . $field,\Magento\Store\Model\ScopeInterface::SCOPE_STORES, $storeId);
    }

}