<?php

namespace Mastering\SampleModule\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Item extends AbstractDb {

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        // TODO: Implement _construct() method.
        $this->_init('mastering_sample_item', 'id');
    }
}