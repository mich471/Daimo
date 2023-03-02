<?php
namespace Softtek\ReverseTransactions\Observer;

use Softtek\ReverseTransactions\Model\Enum\ReverseTransactionsStatusName;
use Softtek\ReverseTransactions\Model\ReverseTransactionsFactory;
use \Magento\Framework\Stdlib\DateTime\TimezoneInterface;


class CheckoutAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * * @var ReverseTransactionsFactory
     */
    protected $reverseTransactionFactory;
    /**
     * * @var TimezoneInterface
     */
    protected $time;

    /**
     * @param ReverseTransactionsFactory $reverseTransactionFactory,
     * @param TimezoneInterface $time
     * @param array $data
     */

    function __construct(
        ReverseTransactionsFactory $reverseTransactionFactory,
        TimezoneInterface $time
    ) {
        $this->reverseTransactionFactory = $reverseTransactionFactory;
        $this->time = $time;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getData('order');

        $paymentMethod = $order->getPayment()->getMethod();

        if ($paymentMethod == 'paypal_express')
        {
            $incrementId = $order->getIncrementId();

            $transactionReverse = $this->reverseTransactionFactory->create();
            $transactionReverse->load($incrementId,'increment_id');

            $orderPayment = $order->getPayment();

            $transactionReverse->setStatus(ReverseTransactionsStatusName::processed);
            $transactionReverse->setTransactionId($orderPayment->getLastTransId());
            $transactionReverse->setIsProcessed(true);
            $transactionReverse->setProcessedDate($this->time->date()->format('Y-m-d h:i:s'));

            try {
                $transactionReverse->save();
            } catch (CouldNotSaveException $e) {
                $this->_logger->error("Error when updating ReverseTransaction for PayPal" .
                " with order number: " . $incrementId . " Message: " . $e->getMessage());
                return null;
            }
        }
    }
}