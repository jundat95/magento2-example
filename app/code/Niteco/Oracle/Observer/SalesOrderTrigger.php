<?php
/**
 * Niteco
 * User: tinh.ngo
 * Date: 2/26/2019
 * Time: 4:12 PM
 */

namespace Niteco\Oracle;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SalesOrderTrigger implements ObserverInterface{

    private $sentOracleLogger;

    public function __construct(
        \Niteco\Oracle\Common\SentOracleLogger $sentOracleLogger
    )
    {
        $this->sentOracleLogger = $sentOracleLogger;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order->getState() == 'pending_payment') {
            $this->sentOracleLogger->logText("observer: order is pending_payment");
        } else
        if ($order->getState() == 'processing') {
            $this->sentOracleLogger->logText("observer: order is processing");
        } else
        if ($order->getState() == 'complete') {
            $this->sentOracleLogger->logText("observer: order is complete");
        }
    }
}