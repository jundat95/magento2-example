<?php
/**
 * Niteco
 * User: tinhngo
 * Date: 2/26/19
 * Time: 10:59 PM
 */

namespace Niteco\Oracle\Model;

use Magento\Framework\Model\AbstractModel;
use Niteco\Oracle\Model\ResourceModel\ScheduleResource;

class Schedule extends AbstractModel {

    protected function _construct()
    {
        $this->_init(ScheduleResource::class);
    }
}