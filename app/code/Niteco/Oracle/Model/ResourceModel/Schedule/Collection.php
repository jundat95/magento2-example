<?php
/**
 * Niteco
 * User: tinhngo
 * Date: 2/26/19
 * Time: 10:59 PM
 */

namespace Niteco\Oracle\Model\ResourceModel\Schedule;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Niteco\Oracle\Model\ResourceModel\ScheduleResource;
use Niteco\Oracle\Model\Schedule;

class Collection extends AbstractCollection {

    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(
            Schedule::class,
            ScheduleResource::class
        );
    }

}