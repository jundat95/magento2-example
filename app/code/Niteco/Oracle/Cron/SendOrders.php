<?php
/**
 * Niteco
 * User: tinh.ngo
 * Date: 2/22/2019
 * Time: 11:02 AM
 */

namespace Niteco\Oracle\Cron;

use Niteco\Oracle\Helper\SentToOracleStatus;

class SendOrders {

    private $sentOracleLogger;
    private $orderManager;
    private $oracleManager;

    public function __construct(
        \Niteco\Oracle\Common\SentOracleLogger $sentOracleLogger,
        \Niteco\Oracle\Helper\OrderManager $orderManager,
        \Niteco\Oracle\Helper\OracleManager $oracleManager
    )
    {
        $this->sentOracleLogger = $sentOracleLogger;
        $this->orderManager = $orderManager;
        $this->oracleManager = $oracleManager;
    }

    public function execute() {
        $oders = $this->orderManager->getOrders();
        foreach ($oders as $order) {
            if ($this->oracleManager->pushOrderToOracle($order)) {
                $this->orderManager->setStatusSentOrder(SentToOracleStatus::SENT_SUCCESS, $order);
            } else {
                $this->sentOracleLogger->logText('Sent to oracle error order: '.$order->getId());
            }
        }
    }
}
