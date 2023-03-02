<?php
namespace Softtek\Marketplace\Model\Order\Email\Sender;

use Magento\Sales\Model\Order\Email\Sender\OrderSender as SenderOrderSender;
use Magento\Sales\Model\Order;
use Magento\Framework\DataObject;

/**
 * Sends order email to the customer.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OrderSender extends SenderOrderSender
{
    /**
     * Prepare email template with variables
     *
     * @param Order $order
     * @return void
     */
    protected function prepareTemplate(Order $order)
    {
        $transport = [
            'order' => $order,
            'order_id' => $order->getId(),
            'billing' => $order->getBillingAddress(),
            'payment_html' => $this->getPaymentHtml($order),
            'store' => $order->getStore(),
            'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
            'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
            'created_at_formatted' => $order->getCreatedAtFormatted(2),
            'order_data' => [
                'customer_name' => $order->getCustomerName(),
                'is_not_virtual' => $order->getIsNotVirtual(),
                'email_customer_note' => $order->getEmailCustomerNote(),
                'frontend_status_label' => $order->getFrontendStatusLabel()
            ]
        ];
        $transportObject = new DataObject($transport);

        /**
         * Event argument `transport` is @deprecated. Use `transportObject` instead.
         */
        $this->eventManager->dispatch(
            'email_order_set_template_vars_before',
            ['sender' => $this, 'transport' => $transportObject, 'transportObject' => $transportObject]
        );

        $this->templateContainer->setTemplateVars($transportObject->getData());

        if ($order->getPayment()->getMethodInstance()->getCode() != "foxsea_paghiper") {
            parent::prepareTemplate($order);
            return $this;
        }
        if ($order->getStatus() != "payment_review") {
            parent::prepareTemplate($order);
            return $this;
        }

        //Template for Boleto Bancario payments
        $this->templateContainer->setTemplateOptions($this->getTemplateOptions());

        if ($order->getCustomerIsGuest()) {
            $templateId = $this->identityContainer->getGuestTemplateId();
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $templateId = $this->globalConfig->getValue ('sales_email/order/boleto_template');
            $customerName = $order->getCustomerName();
        }

        $this->identityContainer->setCustomerName($customerName);
        $this->identityContainer->setCustomerEmail($order->getCustomerEmail());
        $this->templateContainer->setTemplateId($templateId);
    }
}
