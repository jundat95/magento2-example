<?php

namespace Mastering\SampleModule\Block;

use Magento\Framework\View\Element\Template;
use Mastering\SampleModule\Model\ResourceModel\Item\Collection;
use Mastering\SampleModule\Model\ResourceModel\Item\CollectionFactory;

class Hello extends Template {

    private $collectionFactory;

    public function __construct(
        Template\Context $content,
        CollectionFactory $collectionFactory,
        array $data = []
    )
    {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($content, $data);
    }

    /**
     * @return \Mastering\SampleModule\Model\ResourceModel\Item[]
     */
    public function getItems() {
        return $this->collectionFactory->create()->getItems();
    }

}