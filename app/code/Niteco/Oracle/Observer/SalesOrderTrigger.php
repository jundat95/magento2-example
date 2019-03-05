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


class SalesOrderTrigger implements ObserverInterface {

    private $STORE_ID = '1';

    private $sentOracleLogger;
    private $configManager;
    private $scheduleFactory;
    private $queueManager;

    public function __construct(
        \Niteco\Oracle\Helper\QueueManager $queueManager,
        \Niteco\Oracle\Helper\ConfigManager $configManager,
        \Niteco\Oracle\Common\SentOracleLogger $sentOracleLogger,
        \Niteco\Oracle\Model\ScheduleFactory $scheduleFactory
    )
    {
        $this->queueManager = $queueManager;
        $this->configManager = $configManager;
        $this->sentOracleLogger = $sentOracleLogger;
        $this->scheduleFactory = $scheduleFactory;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $storeId = $order->getData('store_id');

        // Validate store US
        if (!empty($this->configManager->getRedisStoreId()) && is_numeric($this->configManager->getRedisStoreId())) {
            $this->STORE_ID = $this->configManager->getRedisStoreId();
        }
        if ($storeId !== $this->STORE_ID) return;

        if ($order instanceof \Magento\Framework\Model\AbstractModel) {
//            if ($order->getStatus() == 'pending') {
//                $this->sentOracleLogger->logText("observer: order is new");
//            }
//            if ($order->getStatus() == 'complete') {
//                $this->sentOracleLogger->logText("observer: order is complete");
//            }
            if ($order->getStatus() == 'processing') {
                // Save orderId to mysql
                $schedule = $this->scheduleFactory->create();
                $schedule->setData('entity_id', $order->getData('entity_id'));
                $schedule->setData('increment_id', $order->getData('increment_id'));
                $schedule->setIsObjectNew(true);
                $schedule->save();
                // Save orderId to redis
                $this->queueManager->pushOrderId($order->getData('entity_id'));
            }

        }

        return $this;
    }
}