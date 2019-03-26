<?php

namespace Mastering\SampleModule\Block;

use Magento\Framework\View\Element\Template;

class Hello extends Template {

    private $collectionFactory;
    protected $orderCollectionFactory;
    protected $order;
    protected $orderFactory;

    public function __construct(
        Template\Context $content,
        \Mastering\SampleModule\Model\ResourceModel\Item\CollectionFactory $collectionFactory,
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
        return $this->collectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('status', 0);
    }

    public function getAllOrders() {

        $to = date("Y-m-d h:i:s"); // current date
        $from = strtotime('-1 day', strtotime($to));
        $from = date('Y-m-d h:i:s', $from); // 2 days before

        $result = $this->orderCollectionFactory->create()
//            ->addAttributeToSelect('*');
              ->addAttributeToSelect('entity_id')
              ->addAttributeToSelect('status')
              ->addAttributeToSelect('store_id')
              ->addAttributeToSelect('sent_to_oracle');
        $data = $result;
//            ->addFieldToFilter('status', 'processing')
//            ->addFieldToFilter('sent_to_oracle', 0)
//            ->addFieldToFilter('created_at', array('from' => $from, 'to' => $to));
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