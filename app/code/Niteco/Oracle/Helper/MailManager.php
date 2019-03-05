<?php
/**
 * Niteco
 * User: tinhngo
 * Date: 3/4/19
 * Time: 10:20 PM
 */

namespace Niteco\Oracle\Helper;

class MailManager {

    const generalEmailDefault = 'tinh.ngo@niteco.se';
    const generalNameDefault = 'Tinh Ngo';

    private $sentEmailLogger;
    private $configManager;
    private $email;

    public function __construct(
        \Niteco\Oracle\Common\SentEmailLogger $sentEmailLogger,
        \Niteco\Oracle\Helper\ConfigManager $configManager,
        \Niteco\Oracle\Common\Email $email
    )
    {
        $this->sentEmailLogger = $sentEmailLogger;
        $this->configManager = $configManager;
        $this->email = $email;
    }

    public function sendMail($message) {

        /**
         * Fetch the e-mail address(es)
         */
        $emails = explode(';', $this->configManager->getEmailReceive());

        $this->sentEmailLogger->logArray('Send email to: '.$emails);

        /**
         * Remove any whitespace
         */
        array_walk($emails, 'trim');


        /**
         * Fetch the general e-mail address
         */
        $generalEmail   = $this->configManager->getGeneralEmail();
        $generalName  = $this->configManager->getGeneralEmail();

//        $this->sentEmailLogger->logText($generalEmail);
//        $this->sentEmailLogger->logText($generalName);

        /**
         * If nothing was found, use our default
         */
        if (!$generalEmail) {
            $generalEmail = self::generalEmailDefault;
        }

        if (!$generalName) {
            $generalName = self::generalNameDefault;
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

}