<?php

namespace  Mastering\SampleModel\Model;

use Magento\Framework\Model\AbstractModel;

class Item extends AbstractModel
{

    protected function _construct()
    {
        $this->_init(\Mastering\SampleModel\Model\ResourceModel\Item::class);
    }
}