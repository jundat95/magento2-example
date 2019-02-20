<?php

namespace Learning\HelloPage\Controller\Page;
use Magento\Framework\Controller\ResultFactory;

class View extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }
    /**
     * View  page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $data = $this->getOrders();
        var_dump($data->getData());
        $result->setContents("=======test======");
        return $result;
    }

    public function getOrders()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('Magento\Sales\Model\Order')
            ->getCollection()
            ->addAttributeToFilter('customer_is_guest', ['eq'=>1]);
        return $order;
    }

    public function getOrders1() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $lastyear = date('Y-m-d', strtotime("-1 year"));
        $order = $objectManager->create('Magento\Sales\Model\Order')->getCollection()->addAttributeToFilter('state', 'complete')->addAttributeToFilter('created_at', ['gteq'  => $lastyear]);
        foreach($order as $orderDatamodel1){

            echo '<pre>'; print_r($orderDatamodel1->getData());

        }
        return $order;
    }


}