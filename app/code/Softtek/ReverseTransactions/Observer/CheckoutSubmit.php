<?php
namespace Softtek\ReverseTransactions\Observer;

use Magento\Framework\Exception\CouldNotSaveException;
use Softtek\ReverseTransactions\Model\Enum\ReverseTransactionsStatusName;
use Softtek\ReverseTransactions\Model\ResourceModel\ReverseTransactions as ReverseTransactionsResource;
use Softtek\ReverseTransactions\Model\ReverseTransactionsFactory;
use Softtek\ReverseTransactions\Model\ReverseTransactions;
use \Magento\Framework\Stdlib\DateTime\TimezoneInterface;


class CheckoutSubmit implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * * @var ReverseTransactionsResource
     */
    protected $reverseTransactionRepository;
    /**
     * * @var ReverseTransactionsFactory
     */
    protected $reverseTransactionFactory;
    /**
     * * @var TimezoneInterface
     */
    protected $time;

    /**
     * @param ReverseTransactionsResource $reverseTransactionRepository
     * @param ReverseTransactionsFactory $reverseTransactionFactory,
     * @param TimezoneInterface $time
     * @param array $data
     */

    function __construct(
        ReverseTransactionsResource $reverseTransactionRepository,
        ReverseTransactionsFactory $reverseTransactionFactory,
        TimezoneInterface $time
    ) {
        $this->reverseTransactionFactory = $reverseTransactionFactory;
        $this->reverseTransactionRepository = $reverseTransactionRepository;
        $this->time = $time;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getData('order');

        $paymentMethod = $order->getPayment()->getMethod();

        if ($paymentMethod == 'paypal_express')
        {
            $incrementId = $order->getIncrementId();
            $data = [
                'customer_email' => $order->getCustomerEmail(),
                'increment_id' => $incrementId,
                'transaction_id' => $order->getPayment()->getLastTransId(),
                'transaction_date' => $this->time->date()->format('Y-m-d h:i:s'),
                'amount' => $order->getGrandTotal(),
                'currency_code' => $order->getBaseCurrencyCode(),
                'status' => $order->getStatus(),
                'payment_method' => $paymentMethod,
                'reverse_error_details' => ''
            ];
    
            $newTransaction = $this->reverseTransactionFactory->create();
            try {
                $this->reverseTransactionRepository->save($newTransaction->addData($data));
            } catch (CouldNotSaveException $e) {
                $this->_logger->error("Error when saving ReverseTransaction for PayPal" .
                " with order number: " . $incrementId . " Message: " . $e->getMessage());
                return null;
            }
        }
    }
}