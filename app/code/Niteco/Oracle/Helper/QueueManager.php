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

    private $KEY = 'nitecoqueue';

    public function __construct()
    {
    }

    /**
     * @param $orderId
     */
    public function pushOrderId($orderId) {
        $redis = new \Credis_Client('localhost');
        $redis->rPush($this->KEY, $orderId);
    }

}