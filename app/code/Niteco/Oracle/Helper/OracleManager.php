<?php
/**
 * Niteco
 * User: tinh.ngo
 * Date: 2/22/2019
 * Time: 3:49 PM
 */

namespace Niteco\Oracle\Helper;

class OracleManager {

    private $sentOracleLogger;

    public function __construct(
        \Niteco\Oracle\Common\SentOracleLogger $sentOracleLogger
    )
    {
        $this->sentOracleLogger = $sentOracleLogger;
    }

    public function pushOrderToOracle($order) {

        $orderJson = json_encode($order);

        $url = 'http://oracle.local/';
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $orderJson);

        // Set HTTP Header for POST request
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($orderJson))
        );

        $response = curl_exec($ch);
        curl_close($ch);

//        $this->sentOracleLogger->logArray($order);
//        $this->sentOracleLogger->logArray($orderJson);
//        $this->sentOracleLogger->logArray($response);

        $this->sentOracleLogger->logText('Pushing order to Oracle.');

        $responseJson = json_decode($response);
        if ($responseJson->status == 'true')
            return true;
        return false;

    }
}