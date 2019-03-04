<?php
/**
 * Niteco
 * User: tinhngo
 * Date: 3/2/19
 * Time: 8:49 PM
 */

namespace Niteco\Oracle\Helper;

use colinmollenhour\credis\Client;

class QueueManager {

    private $redis;

    private $HOST = 'localhost';
    private $PORT = 6379;
    private $PSSWD = '';
    private $KEY = 'nitecoqueue';

    private $sentOracleLogger;

    public function __construct(
        \Niteco\Oracle\Common\SentOracleLogger $sentOracleLogger
    )
    {
        $this->sentOracleLogger = $sentOracleLogger;
        $this->connectRedisServer();
    }

    private function connectRedisServer() {
        try {
            $this->redis = new \Credis_Client($this->HOST, $this->PORT);
        } catch (CredisException $exception) {
            $this->sentOracleLogger->logText('Error: '.$exception->getMessage());
        }
    }

    /**
     * @param $orderId
     */
    public function pushOrderId($orderId) {

        $this->redis->rPush($this->KEY, $orderId);
    }

    public function popOrderId() {

        return $this->redis->lPop($this->KEY);
    }

    public function getAllOrderId() {
        return $this->redis->lRange($this->KEY, 0, -1);
    }

}