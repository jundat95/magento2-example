<?php
/**
 * Niteco
 * User: tinh.ngo
 * Date: 2/26/2019
 * Time: 4:12 PM
 */

namespace Niteco\Oracle\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Niteco\Oracle\Common\SentOracleLogger;

use Niteco\Oracle\Model\ScheduleFactory;

class SalesOrderTrigger implements ObserverInterface {

    private $sentOracleLogger;
    private $scheduleFactory;

    public function __construct(
        SentOracleLogger $sentOracleLogger,
        ScheduleFactory $scheduleFactory
    )
    {
        $this->sentOracleLogger = $sentOracleLogger;
        $this->scheduleFactory = $scheduleFactory;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order instanceof \Magento\Framework\Model\AbstractModel) {
//            if ($order->getStatus() == 'pending') {
//                $this->sentOracleLogger->logText("observer: order is new");
//            }
//            if ($order->getStatus() == 'complete') {
//                $this->sentOracleLogger->logText("observer: order is complete");
//            }
            if ($order->getStatus() == 'processing') {
//                $this->sentOracleLogger->logText("observer: order is processing");
                $schedule = $this->scheduleFactory->create();
                $schedule->setData('entity_id', $order->getData('entity_id'));
                $schedule->setData('increment_id', $order->getData('increment_id'));
                $schedule->setIsObjectNew(true);
                $schedule->save();
            }

        }

        return $this;
    }
}