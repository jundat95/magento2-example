<?php
/**
 * Niteco
 * User: tinh.ngo
 * Date: 2/28/2019
 * Time: 10:19 AM
 */

namespace Niteco\Oracle\Helper;

class ScheduleManager {

    protected $scheduleCollectionFactory;

    public function __construct(
        \Niteco\Oracle\Model\ResourceModel\Schedule\CollectionFactory $scheduleCollectionFactory
    )
    {
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
    }

    public function getOrdersSchedule() {
        $ordersSchedule = $this->scheduleCollectionFactory->create()
            ->addFieldToFilter('status', 0)
            ->setPageSize(10)
            ->setCurPage(1);

        return $ordersSchedule;
    }

    public function getOrdersScheduleById($orderId) {
        $ordersSchedule = $this->scheduleCollectionFactory->create()
            ->addFieldToFilter('entity_id', $orderId);
        return $ordersSchedule;
    }

    public function getOrdersScheduleByStatus($status) {
        $ordersSchedule = $this->scheduleCollectionFactory->create()
            ->addFieldToFilter('status', $status);
        return $ordersSchedule;
    }

    public function changeStatus($status, $schedule) {
        $schedule->setData('status', $status);
        $schedule->save();
    }

    public function setMessage($message, $schedule) {
        $schedule->setData('message', $message);
        $schedule->save();
    }

    public function changeTimeExecute($time, $schedule) {
        $schedule->setData('executed_at', $time);
        $schedule->save();
    }

    public function changeTimeFinished($time, $schedule) {
        $schedule->setData('finished_at', $time);
        $schedule->save();
    }

}