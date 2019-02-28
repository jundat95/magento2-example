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
    private $scheduleManager;

    public function __construct(
        \Niteco\Oracle\Common\SentOracleLogger $sentOracleLogger,
        \Niteco\Oracle\Helper\OrderManager $orderManager,
        \Niteco\Oracle\Helper\OracleManager $oracleManager,
        \Niteco\Oracle\Helper\ScheduleManager $scheduleManager
    )
    {
        $this->sentOracleLogger = $sentOracleLogger;
        $this->orderManager = $orderManager;
        $this->oracleManager = $oracleManager;
        $this->scheduleManager = $scheduleManager;
    }

    public function execute() {

        $ordersSchedule = $this->scheduleManager->getOrdersSchedule();

        foreach ($ordersSchedule as $orderItem) {
            $order = $this->orderManager->getOrderById($orderItem->getData('entity_id'));
            $orderData = $this->orderManager->getOrderData($order);
            if ($this->oracleManager->pushOrderToOracle($orderData)) {
                $this->sentOracleLogger->logText('sent success to oracle');
                $this->orderManager->addOrderComment('Transfer order # '.$order->getData('increment_id').' to Oracle is success', $order);
            } else {
                $this->sentOracleLogger->logText('sent fail to oracle');
            }
        }


    }
}
