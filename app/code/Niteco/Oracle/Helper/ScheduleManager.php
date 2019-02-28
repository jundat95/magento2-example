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
        $ordersSchedule = $this->scheduleCollectionFactory->create();
//            ->addAttributeToSelect('*');
//            ->addFieldToFilter('status', 0);

        return $ordersSchedule;

    }
}