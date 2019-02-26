<?php
/**
 * Niteco
 * User: tinh.ngo
 * Date: 2/22/2019
 * Time: 3:15 PM
 */

namespace Niteco\Oracle\Helper;

class OrderManager {

    private $sentOracleLogger;
    protected $orderCollectionFactory;
    protected $order;

    public function __construct(
        \Niteco\Oracle\Common\SentOracleLogger $sentOracleLogger,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Api\Data\OrderInterface $order
    )
    {
        $this->sentOracleLogger = $sentOracleLogger;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->order = $order;
    }

    public function getOrders() {

        $to = date("Y-m-d h:i:s"); // current date
        $from = strtotime('-1 day', strtotime($to));
        $from = date('Y-m-d h:i:s', $from); // 2 days before

        $data = $this->orderCollectionFactory->create()
            ->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('status')
            ->addAttributeToSelect('store_id')
            ->addAttributeToSelect('sent_to_oracle');
//        ->addFieldToFilter('status', 'processing')
//        ->addFieldToFilter('sent_to_oracle', 0)
//        ->addFieldToFilter('store_id', 2)
//        ->addFieldToFilter('created_at', array('from' => $from, 'to' => $to));

        return $data;
    }

    public function getOrderById($orderId) {
        return $this->order->load($orderId);
    }

    public function getOrderData($orderId) {
        
        $order = $this->getOrderById($orderId);

        $orderData = $order->getData();
        $shippingAddress = $order->getShippingAddress()->getData();
        $billingAddress = $order->getBillingAddress()->getData();
        $orderData['shippingAddress'] = $shippingAddress;
        $orderData['billingAddress'] = $billingAddress;
        
        $allItems = [];
        $allItemsResult = $order->getAllItems();
        foreach($allItemsResult as $item) {
            array_push($allItems, $item->getData());
        }
        $orderData['allItems'] = $allItems;

        return $orderData;
    }

    public function setStatusSentOrder($status, $order) {
        $order->setData('sent_to_oracle', $status);
        $order->save();
    }

    public function addOrderComment($comment, $order) {
        $order->addStatusHistoryComment($comment)
            ->setIsVisibleOnFront(false)
            ->setIsCustomerNotified(false);
        $order->save();
    }



}