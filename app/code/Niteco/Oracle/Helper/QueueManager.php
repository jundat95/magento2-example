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
    private $KEY = 'nitecoqueue';

    public function __construct()
    {
        $this->redis = new \Credis_Client('localhost');
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