<?php

namespace Mastering\SampleModule\Block;

use Magento\Framework\View\Element\Template;
use Mastering\SampleModule\Model\ResourceModel\Item\CollectionFactory;

class Hello extends Template {

    private $collectionFactory;
    protected $_orderCollectionFactory;
    protected $_orderRepository;

    public function __construct(
        Template\Context $content,
        CollectionFactory $collectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        array $data = []
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this ->_orderRepository = $orderRepository;
        parent::__construct($content, $data);
    }

    /**
     * @return \Mastering\SampleModule\Model\ResourceModel\Item[]
     */
    public function getItems() {
        return $this->collectionFactory->create()->getItems();
    }

    public function getAllOrders() {
        $result = $this->_orderCollectionFactory->create()->addAttributeToSelect('*');
        $data = $result->addFieldToFilter('status', 'pending');
        return $data;
    }

    public function getOrderById() {
       return 'end';
    }

}