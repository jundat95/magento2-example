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
    private $email;
    private $sentEmailLogger;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Niteco\Oracle\Common\Email $email,
        \Niteco\Oracle\Common\SentEmailLogger $sentEmailLogger
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->email = $email;
        $this->sentEmailLogger = $sentEmailLogger;

        parent::__construct($context);
    }

    public function getEmailReceive() {
        $emailReceive = $this->getConfig('send_mail', 'niteco_oracle_email_receive');
        return $emailReceive;
    }

    public function isSendOrderEnable() {
        return $this->getConfig('general', 'enabled');
    }

    public function isSendMailEnable() {
        return $this->getConfig('send_mail', 'enabled');
    }

    public function sendMail($message) {

        /**
         * Fetch the e-mail address(es)
         */
        $emails = explode(';', $this->getEmailReceive());


        /**
         * Remove any whitespace
         */
        array_walk($emails, 'trim');


        /**
         * Fetch the general e-mail address
         */
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $generalEmail   = $this->scopeConfig->getValue('trans_email/ident_general/email', $storeScope);
        $generalName  = $this->scopeConfig->getValue('trans_email/ident_general/name', $storeScope);


        /**
         * If nothing was found, use our default
         */
        if (!$generalEmail) {
            $generalEmail = 'tinh.ngo@niteco.se';
        }

        if (!$generalName) {
            $generalName = 'Magento';
        }

        /**
         * Send message to each recipient
         */
        foreach ($emails as $emailAddress) {
            try {

                $emailTempVariables = array();
                $emailTempVariables['message'] = $message;
                $senderInfo = [
                    'name' => $generalName,
                    'email' => $generalEmail,
                ];
                $receiverInfo = [
                    'name' => 'Receiver',
                    'email' => $emailAddress
                ];


                $this->email->yourCustomMailSendMethod(
                    $emailTempVariables,
                    $senderInfo,
                    $receiverInfo
                );

            } catch (Exception $e) {
                $this->sentEmailLogger->logText($e->getMessage());
            }
        }

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