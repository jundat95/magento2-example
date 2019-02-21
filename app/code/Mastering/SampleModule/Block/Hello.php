<?php

namespace Mastering\SampleModule\Block;

use Magento\Framework\View\Element\Template;
use Mastering\SampleModule\Model\ResourceModel\Item\CollectionFactory;

class Hello extends Template {

    private $collectionFactory;
    protected $orderCollectionFactory;
    protected $order;
    protected $orderFactory;

    public function __construct(
        Template\Context $content,
        CollectionFactory $collectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        array $data = []
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->order = $order;
        $this->orderFactory = $orderFactory;

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

    public function getOrderByIdNew($orderId) {
        $order = $this->orderFactory->create()->load($orderId);
        $orderItems = $order->getAllItems();
        foreach ($orderItems as $item){
            echo '</br>';
            print_r($item->getQtyOrdered());
            print_r($item->getDescription());
            print_r($item->getName());
            print_r($item->getPrice());
        }
        return $orderItems;
    }

}