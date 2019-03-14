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
    private $scheduleManager;
    private $queueManager;
    private $timezoneInterface;
    private $configManager;

    public function __construct(
        \Niteco\Oracle\Common\SentOracleLogger $sentOracleLogger,
        \Niteco\Oracle\Helper\OrderManager $orderManager,
        \Niteco\Oracle\Helper\OracleManager $oracleManager,
        \Niteco\Oracle\Helper\ScheduleManager $scheduleManager,
        \Niteco\Oracle\Helper\QueueManager $queueManager,
        \Niteco\Oracle\Helper\ConfigManager $configManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
    )
    {
        $this->sentOracleLogger = $sentOracleLogger;
        $this->orderManager = $orderManager;
        $this->oracleManager = $oracleManager;
        $this->scheduleManager = $scheduleManager;
        $this->queueManager = $queueManager;
        $this->configManager = $configManager;
        $this->timezoneInterface = $timezoneInterface;
    }

    public function execute() {

        $isSendOrderEnable = $this->configManager->isSendOrderEnable();
        if (empty($isSendOrderEnable) || $isSendOrderEnable === "0") return;

        $this->sendOrdersWithRedis(1);

    }

    public function executeQueue2() {

        $isSendOrderEnable = $this->configManager->isSendOrderEnable();
        if (empty($isSendOrderEnable) || $isSendOrderEnable === "0") return;

        $this->sendOrdersWithRedis(2);

    }

    public function executeQueue3() {

        $isSendOrderEnable = $this->configManager->isSendOrderEnable();
        if (empty($isSendOrderEnable) || $isSendOrderEnable === "0") return;

        $this->sendOrdersWithRedis(3);

    }

    public function sendOrdersWithRedis($queueCurrent = 1) {

        if ($queueCurrent === 1) {
            $orderId = $this->queueManager->popOrderId();
        } else if ($queueCurrent === 2) {
            $orderId = $this->queueManager->popOrderIdQueue2();
        } else if ($queueCurrent === 3) {
            $orderId = $this->queueManager->popOrderIdQueue3();
        }

        if ($orderId) {

            // get order
            $order = $this->orderManager->getOrderById($orderId);
            $orderData = $this->orderManager->getOrderData($order);

            // get order schedule
            $schedules = $this->scheduleManager->getOrdersScheduleById($orderId);
            $schedule = $schedules->getFirstItem();

            $this->scheduleManager->changeStatus(SentToOracleStatus::SENDING, $schedule);
            $currentTime = $this->timezoneInterface->date()->getTimestamp();
            $this->scheduleManager->changeTimeExecute($currentTime, $schedule);

            if ($this->oracleManager->pushOrderToOracle($orderData, $schedule)) {

                $this->orderManager->addOrderComment('Transferred to Oracle with order # '.$order->getData('increment_id'), $order);
                $this->scheduleManager->changeStatus(SentToOracleStatus::SENT_SUCCESS, $schedule);
                $currentTime = $this->timezoneInterface->date()->getTimestamp();
                $this->scheduleManager->changeTimeFinished($currentTime, $schedule);
            } else {

                $this->orderManager->addOrderComment('Not Transferred to Oracle with order # '.$order->getData('increment_id'), $order);
                $this->scheduleManager->changeStatus(SentToOracleStatus::SENT_FAIL, $schedule);

                // add orderId to queue
                if ($queueCurrent === 1) {
                    $this->queueManager->pushOrderIdQueue2($orderId);
                } else if ($queueCurrent === 2) {
                    $this->queueManager->pushOrderIdQueue3($orderId);
                }

            }

        } else {
//            $this->sentOracleLogger->logText('queue is empty');
        }

    }


    public function sendOrdersWithMySQL() {
        $schedules = $this->scheduleManager->getOrdersSchedule();

        foreach ($schedules as $schedule) {
            $order = $this->orderManager->getOrderById($schedule->getData('entity_id'));
            $orderId = $order->getData('increment_id');

            $orderData = $this->orderManager->getOrderData($order);

            $this->scheduleManager->changeStatus(SentToOracleStatus::SENDING, $schedule);
            $currentTime = $this->timezoneInterface->date()->getTimestamp();
            $this->scheduleManager->changeTimeExecute($currentTime, $schedule);

            if ($this->oracleManager->pushOrderToOracle($orderData)) {
                $this->sentOracleLogger->logText('Sent order '.$orderId.' to Oracle is success');

                $this->orderManager->addOrderComment('Transfer order # '.$orderId.' to Oracle is success', $order);
                $this->scheduleManager->changeStatus(SentToOracleStatus::SENT_SUCCESS, $schedule);
                $currentTime = $this->timezoneInterface->date()->getTimestamp();
                $this->scheduleManager->changeTimeFinished($currentTime, $schedule);
            } else {
                $this->sentOracleLogger->logText('Sent order '.$orderId.' to Oracle is fail');

                $this->scheduleManager->changeStatus(SentToOracleStatus::SENT_FAIL, $schedule);
                $this->scheduleManager->setMessage('Sent order '.$orderId.' to Oracle is fail', $schedule);
            }
        }
    }

}
