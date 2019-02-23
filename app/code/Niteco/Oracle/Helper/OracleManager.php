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
        $this->sentOracleLogger->logText('Push order #'.$order->getId().' to Oracle.');
        return false;
    }
}