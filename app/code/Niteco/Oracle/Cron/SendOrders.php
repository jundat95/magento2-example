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
    private $timezoneInterface;

    public function __construct(
        \Niteco\Oracle\Common\SentOracleLogger $sentOracleLogger,
        \Niteco\Oracle\Helper\OrderManager $orderManager,
        \Niteco\Oracle\Helper\OracleManager $oracleManager,
        \Niteco\Oracle\Helper\ScheduleManager $scheduleManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
    )
    {
        $this->sentOracleLogger = $sentOracleLogger;
        $this->orderManager = $orderManager;
        $this->oracleManager = $oracleManager;
        $this->scheduleManager = $scheduleManager;
        $this->timezoneInterface = $timezoneInterface;
    }

    public function execute() {

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
