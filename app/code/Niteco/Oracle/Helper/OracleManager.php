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
    private $url = 'https://2074147.restlets.api.netsuite.com/app/site/hosting/restlet.nl?script=78&deploy=1';

    public function __construct(
        \Niteco\Oracle\Common\SentOracleLogger $sentOracleLogger
    )
    {
        $this->sentOracleLogger = $sentOracleLogger;
    }

    public function pushOrderToOracle($order) {

        $orderJson = json_encode($order);

        $ch = curl_init();

        $headers=  array(
            'cache-control:no-cache',
            'Authorization:NLAuth nlauth_account=2074147, nlauth_email=netsuite@claremont.se, nlauth_signature=Integration1, nlauth_role=1004',
            'Content-Type:application/json'
        );

        // Set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $this->url);

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

        $this->sentOracleLogger->logText($response);

//        $this->sentOracleLogger->logArray($order);
//        $this->sentOracleLogger->logArray($orderJson);
//        $this->sentOracleLogger->logArray($response);

        $random = random_int(0, 1);
        $status = $random ? true : false;
        $responseJson = json_decode('{"success":'.$status.',"orderID":null,"error_message":"failed reason"}');

        if ($responseJson->success)
            return true;
        return false;

    }
}