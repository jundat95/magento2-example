<?php
/**
 * Niteco
 * User: tinh.ngo
 * Date: 2/22/2019
 * Time: 11:02 AM
 */

namespace Niteco\Oracle\Cron;


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

        $ordersSchedule = $this->orderManager->getOrdersSchedule();
        $this->sentOracleLogger->logArray($ordersSchedule->getData());

    }
}
