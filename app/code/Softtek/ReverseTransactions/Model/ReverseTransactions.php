<?php
/**
 * Softtek ReverseTransaction Module
 *
 * @package SofttekReverseTransaction
 * @author Juan C Flores <juan.floress@softtek.com>
 * @author Paul Soberannes <paul.soberanes@softtek.com>
 * @copyright Softtek 2020
 */
namespace Softtek\ReverseTransactions\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Softtek\ReverseTransactions\Model\ResourceModel\ReverseTransactions as ResourceModel;

class ReverseTransactions extends AbstractModel
{
    const CACHE_TAG = 'softtek_reverse_transactions';

    /**
     * Init
     */
    protected function _construct() // phpcs:ignore PSR2.Methods.MethodDeclaration
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @inheritDoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @param int $transaction_id
     * @return $this
     */
    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData('id', $id);
    }

    /**
     * @return int
     */
    public function getCustomerEmail()
    {
        return $this->getData('customer_email');
    }

    /**
     * @param int $customer_id
     * @return $this
     */
    public function setCustomerEmail($customer_email)
    {
        return $this->setData('customer_email', $customer_email);
    }

    /**
     * @return string
     */
    public function getIncrementId()
    {
        return $this->getData('increment_id');
    }

    /**
     * @param string $buy_order
     * @return ReverseTransactions
     */
    public function setIncrementId($increment_id)
    {
        return $this->setData('increment_id', $increment_id);
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->getData('transaction_id');
    }

    /**
     * @param string $buy_order
     * @return ReverseTransactions
     */
    public function setTransactionId($transaction_id)
    {
        return $this->setData('transaction_id', $transaction_id);
    }

    /**
     * @return string
     */
    public function getTransactionDate()
    {
        return $this->getData('transaction_date');
    }

    /**
     * @param string $transaction_date
     * @return $this
     */
    public function setTransactionDate($transaction_date)
    {
        return $this->setData('transaction_date', $transaction_date);
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->getData('getAmount');
    }

    /**
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        return $this->setData('amount', $amount);
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->getData('currency_code');
    }

    /**
     * @param float $amount
     * @return $this
     */
    public function setCurrencyCode($currency_code)
    {
        return $this->setData('currency_code', $currency_code);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getData('status');
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData('status', $status);
    }

    /**
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->getData('payment_method');
    }

    /**
     * @param $payment_method
     * @return $this
     */
    public function setPaymentMethod($payment_method)
    {
        return $this->setData('payment_method', $payment_method);
    }

    /**
     * @return string
     */
    public function getPaymentTypeCode()
    {
        return $this->getData('payment_type_code');
    }

    /**
     * @param $payment_type_code
     * @return $this
     */
    public function setPaymentTypeCode($payment_type_code)
    {
        return $this->setData('payment_type_code', $payment_type_code);
    }

    /**
     * @return boolean
     */
    public function getIsProcessed()
    {
        return $this->getData('is_processed');
    }

    /**
     * @param $is_processed
     * @return $this
     */
    public function setIsProcessed($is_processed)
    {
        return $this->setData('is_processed', $is_processed);
    }

    /**
     * @return string
     */
    public function getProcessedDate()
    {
        return $this->getData('processed_date');
    }

    /**
     * @param string $processed_date
     * @return $this
     */
    public function setProcessedDate(string $processed_date)
    {
        return $this->setData('processed_date', $processed_date);
    }

    /**
     * @return bool
     */
    public function getHasError()
    {
        return $this->getData('has_error');
    }

    /**
     * @param bool $has_error
     * @return $this
     */
    public function setHasError($has_error)
    {
        return $this->setData('has_error', $has_error);
    }

    /**
     * @return string
     */
    public function getReverseErrorDetails()
    {
        return $this->getData('reverse_error_details');
    }

    /**
     * @param $reverse_error_details
     * @return $this
     */
    public function setReverseErrorDetails($reverse_error_details)
    {
        return $this->setData('reverse_error_details', $reverse_error_details);
    }
}
