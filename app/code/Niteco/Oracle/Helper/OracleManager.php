<?php
/**
 * Niteco
 * User: tinh.ngo
 * Date: 2/22/2019
 * Time: 3:49 PM
 */

namespace Niteco\Oracle\Helper;

class OracleManager {

    private $url = 'https://2074147.restlets.api.netsuite.com/app/site/hosting/restlet.nl?script=78&deploy=1';
    private $auth = 'Authorization:NLAuth nlauth_account=2074147, nlauth_email=netsuite@claremont.se, nlauth_signature=Integration1, nlauth_role=1004';

    private $sentOracleLogger;
    private $configManager;

    private $apiTimeout = 5;

    public function __construct(
        \Niteco\Oracle\Common\SentOracleLogger $sentOracleLogger,
        \Niteco\Oracle\Helper\ConfigManager $configManager
    )
    {
        $this->sentOracleLogger = $sentOracleLogger;
        $this->configManager = $configManager;
    }

    public function pushOrderToOracle($order, $schedule) {

        $sentSuccess = true;

        // get config from admin
        if (!empty($this->configManager->getOracleEndpoint())) {
            $this->url = $this->configManager->getOracleEndpoint();
        }
        if (!empty($this->configManager->getOracleAuth())) {
            $this->auth = $this->configManager->getOracleAuth();
        }
        if (!empty($this->configManager->getApiTimeout())) {
            $this->apiTimeout = $this->configManager->getApiTimeout();
        }

        $orderJson = json_encode($order);

        $ch = curl_init();

        $headers=  array(
            'cache-control:no-cache',
            'Content-Type:application/json',
            $this->auth
        );

        // Set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $this->url);

        //timeout in seconds
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->apiTimeout);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $orderJson);

        // Set HTTP Header for POST request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        // check response empty
        if (empty($response)) {
            $this->sentOracleLogger->logText('Order # '.$order['increment_id'].' error message: response send by Oracle is empty');
            $schedule->setMessage('response send by Oracle is empty');
            $sentSuccess = false;
            return $sentSuccess;
        }

        $responseJson = json_decode($response);

        if (array_key_exists('success', $responseJson)) {
            if (!$responseJson->success) {
                $this->sentOracleLogger->logText($response);
                $schedule->setMessage($response);
                $sentSuccess = false;
            }
        } else {
            $this->sentOracleLogger->logText('Order # '.$order['increment_id'].' error message: invalid params success');
            $schedule->setMessage('invalid params: success');
            $sentSuccess = false;
        }
        return $sentSuccess;

    }
}