<?php
namespace Nxtech\CheckoutField\Plugin\Checkout\Model;

use Magento\Quote\Model\QuoteRepository;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Customer\Api\AddressRepositoryInterface;

class ShippingInformationManagement
{
    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    protected $_session;

    /**
     * @var AddressRepositoryInterface
     */
    protected $addressRepositoryInterface;

    /**
     * ShippingInformationManagement constructor.
     *
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        \Magento\Customer\Model\Session $session,
        AddressRepositoryInterface $addressRepositoryInterface
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->_session = $session;
        $this->addressRepositoryInterface = $addressRepositoryInterface;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        $extAttributes = $addressInformation->getExtensionAttributes();
        $shippingAddress = $addressInformation->getShippingAddress();
        $billingAddress = $addressInformation->getBillingAddress();
        $cusAddId = $shippingAddress->getData('customer_address_id');

        $shippingAddressExtensionAttributes = $shippingAddress->getExtensionAttributes();

        if ($this->_session->isLoggedIn() && $cusAddId) {
            $customerAddressData = $this->getCustomerAddress($cusAddId);
            $deliveryNote = ($customerAddressData->getCustomAttribute('delivery_note')) ? $customerAddressData->getCustomAttribute('delivery_note')->getValue() : '';
            $shippingAddress->setDeliveryNote($deliveryNote);
            $billingAddress->setDeliveryNote($deliveryNote);
        } else {
            if ($shippingAddressExtensionAttributes) {
                $deliveryNote = $shippingAddressExtensionAttributes->getDeliveryNote();
                $shippingAddress->setDeliveryNote($deliveryNote);
                $billingAddress->setDeliveryNote($deliveryNote);
            }
        }
    }
    public function getCustomerAddress($addressId)
    {
        $addressRepository = $this->addressRepositoryInterface->getById($addressId);
        return $addressRepository;
    }
}