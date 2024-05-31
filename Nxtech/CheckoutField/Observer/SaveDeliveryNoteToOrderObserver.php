<?php
namespace Nxtech\CheckoutField\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class SaveDeliveryNoteToOrderObserver implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $quote = $observer->getQuote();
        $order = $observer->getOrder();
        $deliveryNote = $quote->getShippingAddress()->getDeliveryNote();
        if ($deliveryNote = $quote->getShippingAddress()->getDeliveryNote()) {
            $order->getShippingAddress()->setDeliveryNote($deliveryNote);
            $order->getBillingAddress()->setDeliveryNote($deliveryNote);
        }
    }
}