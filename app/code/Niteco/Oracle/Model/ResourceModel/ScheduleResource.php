<?php
/**
 * Niteco
 * User: tinhngo
 * Date: 2/26/19
 * Time: 11:03 PM
 */

namespace Niteco\Oracle\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ScheduleResource extends AbstractDb {

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('niteco_oracle_schedule', 'id');
    }
}