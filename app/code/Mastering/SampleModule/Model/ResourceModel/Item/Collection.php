<?php

namespace  Mastering\SampleModel\Model\ResourceModel\Item;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mastering\SampleModel\Model\Item;
use Mastering\SampleModel\Model\ResourceModel\Item as ItemResource;

class Collection extends AbstractCollection {

    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(Item::class, ItemResource::clas );
    }
}