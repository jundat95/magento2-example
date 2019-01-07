<?php

namespace  Mastering\SampleModel\Model\ResourceModel;


use Magento\Framework\Model\ResourceModel\Db\AbstractDb;


class Item extends  AbstractDb {

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mastering_sample_item', 'id');
    }
}
