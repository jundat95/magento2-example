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
    private $configManager;

    public function __construct(
        \Niteco\Oracle\Common\SentOracleLogger $sentOracleLogger,
        \Niteco\Oracle\Helper\ConfigManager $configManager
    )
    {
        $this->sentOracleLogger = $sentOracleLogger;
        $this->configManager = $configManager;
        $this->connectRedisServer();
    }

    private function connectRedisServer() {

        if (!empty($this->configManager->getRedisHost())) {
            $this->HOST = $this->configManager->getRedisHost();
        }
        if (!empty($this->configManager->getRedisPort()) && is_numeric($this->configManager->getRedisPort())) {
            $this->PORT = (int)$this->configManager->getRedisPort();
        }
        if (!empty($this->configManager->getRedisPassword())) {
            $this->PSSWD = $this->configManager->getRedisPassword();
        }
        if (!empty($this->configManager->getRedisQueueKey())) {
            $this->KEY = $this->configManager->getRedisQueueKey();
        }

        try {
            $this->redis = new \Credis_Client($this->HOST, $this->PORT);
            if (!empty($this->PSSWD)) {
                $this->redis->auth($this->PSSWD);
            }
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