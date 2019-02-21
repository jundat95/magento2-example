<?php

namespace Mastering\SampleModule\Block;

use Magento\Framework\View\Element\Template;
use Mastering\SampleModule\Model\ResourceModel\Item\CollectionFactory;

class Hello extends Template {

    private $collectionFactory;
    protected $orderCollectionFactory;
    public $order;

    public function __construct(
        Template\Context $content,
        CollectionFactory $collectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Api\Data\OrderInterface $order,
        array $data = []
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->order = $order;

        parent::__construct($content, $data);
    }

    /**
     * @return \Mastering\SampleModule\Model\ResourceModel\Item[]
     */
    public function getItems() {
        return $this->collectionFactory->create()->getItems();
    }

    public function getAllOrders() {
        $result = $this->orderCollectionFactory->create()->addAttributeToSelect('*');
        $data = $result;//->addFieldToFilter('status', 'pending');
        return $data;
    }

    public function getOrderById($orderId) {
        $order = $this->order->load($orderId);
        return $order;
    }

}