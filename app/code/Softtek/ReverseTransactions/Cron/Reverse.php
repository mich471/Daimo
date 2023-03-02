<?php
/**
 * Softtek Reverse Transactions Module
 *
 * @package SofttekReverseTransactions
 * @author Jorge Serena <jorge.serena@softtek.com>
 * @copyright Softtek 2021
 */
declare(strict_types=1);

namespace Softtek\ReverseTransactions\Cron;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\CouldNotSaveException;
use Softtek\ReverseTransactions\Model\Enum\ReverseTransactionsPaymentMethods;
use Softtek\ReverseTransactions\Model\Enum\ReverseTransactionsStatusName;
use Softtek\ReverseTransactions\Model\ResourceModel\ReverseTransactions as ReverseTransactionsResource;
use Softtek\ReverseTransactions\Model\ResourceModel\ReverseTransactions\CollectionFactory as ReverseTransactionsCollectionFactory;
use Softtek\ReverseTransactions\Helper\SendEmailHelper;
use Softtek\ReverseTransactions\Helper\ConfigHelper;

use Softtek\Payment\Model\Cybersource\Transaction as CybserSourceTransation;

use Softtek\ReverseTransactions\Model\LoggerCsv;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Reverse
{
    const CYBERSOURCE_STATUS_PENDING = 'PENDING';
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var ReverseTransactionsResource
     */
    private $repository;
    /**
     * @var TimezoneInterface
     */
    private $dateTime;
    /**
     * @var LoggerCsv
     */
    private $loggerCsv;
    /**
     * @var SendEmailHelper
     */
    private $emailHelper;
    /**
     * @var Config
     */
    protected $_configHelper;
    /**
     * @var CybserSourceTransation
     */
    protected $transaction;
    /**
     * @var ReverseTransactionsCollectionFactory
     */
    protected $reverseTransactionCollectionFactory;

    /**
     * Constructor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ReverseTransactionsResource $repository
     * @param LoggerCsv $loggerCsv
     * @param TimezoneInterface $dateTime
     * @param SendEmailHelper $emailHelper
     * @param \Psr\Log\LoggerInterface $logger
     * @param CybserSourceTransation
     * @param ReverseTransactionCollectionFactory $reverseTransactionCollection
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ReverseTransactionsResource $repository,
        LoggerCsv $loggerCsv,
        TimezoneInterface $dateTime,
        SendEmailHelper $emailHelper,
        \Psr\Log\LoggerInterface $logger,
        ConfigHelper $configHelper,
        CybserSourceTransation $transaction,
        ReverseTransactionsCollectionFactory $reverseTransactionCollectionFactory
    )
    {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->repository = $repository;
        $this->loggerCsv = $loggerCsv;
        $this->dateTime = $dateTime;
        $this->emailHelper = $emailHelper;
        $this->logger = $logger;
        $this->_configHelper = $configHelper;
        $this->transaction = $transaction;
        $this->reverseTransactionCollectionFactory = $reverseTransactionCollectionFactory;
    }

    /**
     * Execute the cron
     * Get not completed transactions and call TransBank API refund method
     * to reverse transaction on their side
     *
     * @return void
     */
    public function execute()
    {
        $this->logger->info("Cronjob for Payment Transaction Reverse is starting.");
        $this->reverseCyberSourcePayments();
        $this->notifyPayPalPayments();
    }

    private function reverseCyberSourcePayments()
    {
        $transactionCollection = $this->reverseTransactionCollectionFactory->create();

        $nowTime = $this->dateTime->date()->format('Y-m-d H:i:s');
        $dateTimeFrom = new \DateTime($nowTime);
        $dateTimeFrom->modify("-5 minutes");
        $dateTimeFrom = $dateTimeFrom->format('Y-m-d H:i:s');

        //Get open orders with no corresponding data in payment provider and get also canceled transactions via Credit Memo
        $transactions = $transactionCollection
            ->addFieldToFilter('is_processed', ['eq' => false])
            ->addFieldToFilter('payment_method', ['eq' => ReverseTransactionsPaymentMethods::CYBERSOURCE_CODE])
            ->addFieldToFilter('transaction_date', ['lt' => $dateTimeFrom])
            ->load();

        $transactionsLog = [];

        foreach ($transactions as $transaction) 
        {
            $transaction->setData('processed_date', $this->dateTime->date()->format('Y-m-d H:i:s'));
            $transaction->setData('is_processed', true);
            try 
            {
                $refundResponse = $this->transaction->processRefund([
                    'code' => $transaction->getData('increment_id'),
                    'transactionId' => $transaction->getData('transaction_id'),
                    'currency' => $transaction->getData('currency_code'),
                    'totalAmount' => $transaction->getData('amount'),
                ]);

                $refundStatus = $refundResponse->getStatus();

                if ($this::CYBERSOURCE_STATUS_PENDING != $refundStatus)
                {
                    $transaction->setData('reverse_error_details', $refundResponse->getMessage());
                    $transaction->setData('has_error', true);
                }
                else 
                {
                    $transaction->setData('transaction_id', $refundResponse->getReconciliationId());
                    $transaction->setData('has_error', false);
                }

                $transaction->setData('status', $refundStatus);

                //@TODO Imprimir contenidos de objeto de respuesta para cambiar estatus de reversa
                $transactionsLog[] = $transaction;
            } catch (\Exception $exception) {
                $exceptionMessage = $exception->getMessage();
                $this->logger->error("Failed to nullify transaction on Cyber Source API. " .
                    " ReverseTransaction with ID: " .
                    $transaction->getId() . " for order number: " .
                    $transaction->getIncrementId() . " " .
                    $exceptionMessage);
                $transaction->setData('reverse_error_details', $exceptionMessage);
            }

            try {
                $this->repository->save($transaction);
            } catch (\Exception $exception) {
                $this->logger->error("Failed to save Transaction Reverse" .
                    " with ID: " .
                    $transaction->getId() . " Error: " . $exceptionMessage);
            }
        }

        if ($transactionsLog && count($transactionsLog) > 0) {
            $mailTitle = 'El proceso de cancelacion/reversa CYBERSOURCE se ha ejecutado.';
            $this->loggerCsv->writeToCsv($transactionsLog, ReverseTransactionsPaymentMethods::CYBERSOURCE_CODE);
            $this->emailHelper->sendReverseNotification($transactionsLog, ReverseTransactionsPaymentMethods::CYBERSOURCE_CODE, $mailTitle);
        }
    }

    private function notifyPayPalPayments()
    {
        $transactionCollection = $this->reverseTransactionCollectionFactory->create();

        $nowTime = $this->dateTime->date()->format('Y-m-d H:i:s');
        $dateTimeFrom = new \DateTime($nowTime);
        $dateTimeFrom->modify("-10 minutes");
        $dateTimeFrom = $dateTimeFrom->format('Y-m-d H:i:s');

        //Get open orders with no corresponding data in payment provider and get also canceled transactions via Credit Memo
        $transactions = $transactionCollection
            ->addFieldToFilter('is_processed', ['eq' => false])
            ->addFieldToFilter('payment_method', ['eq' => ReverseTransactionsPaymentMethods::PAYPAL_EXPRESS_CODE])
            ->addFieldToFilter('transaction_date', ['lt' => $dateTimeFrom])
            ->load();

        $transactionsLog = [];
        foreach ($transactions as $transaction) 
        {
            $transaction->setData('processed_date', $this->dateTime->date()->format('Y-m-d H:i:s'));
            $transaction->setData('is_processed', true);
            
            try {
                $this->repository->save($transaction);
            } catch (\Exception $exception) {
                $this->logger->error("Failed to update Transaction Reverse" .
                    " with ID: " .
                    $transaction->getId() . " Error: " . $exceptionMessage);
            }

            $transactionsLog[] = $transaction;
        }

        if ($transactionsLog && count($transactionsLog) > 0) {
            $mailTitle = 'Notificación de transacciones de PayPal incompletas.';
            $this->loggerCsv->writeToCsv($transactionsLog, ReverseTransactionsPaymentMethods::PAYPAL_EXPRESS_CODE);
            $this->emailHelper->sendReverseNotification($transactionsLog, ReverseTransactionsPaymentMethods::PAYPAL_EXPRESS_CODE, $mailTitle);
        }
    }
}
