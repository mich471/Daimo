<?php

/**
 * Config Provider Class
 *
 * @package Softtek_Payment
 * @author Paul Soberanes <paul.soberanes@softtek.com>, Jorge Serena <jorge.serena@softtek.com>
 * @copyright © Softtek. All rights reserved.
 */

namespace Softtek\Payment\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Softtek\MonitorIntegration\Helper\SchedulesMessagesHelper;

use Softtek\ReverseTransactions\Model\Enum\ReverseTransactionsStatusName;
use Softtek\ReverseTransactions\Model\ResourceModel\ReverseTransactions as ReverseTransactionsResource;
use Softtek\ReverseTransactions\Model\ReverseTransactionsFactory;
use Softtek\ReverseTransactions\Model\ReverseTransactions;
use \Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Payment extends \Magento\Payment\Model\Method\Cc
{
    const METHOD_CODE = 'softtek_payment';
    const AUTHORIZED_STATUS = 'AUTHORIZED';

    /**
     * @var string
     */
    protected $_code = self::METHOD_CODE;

    /**
     * @var MagentoPaymentModelMethodLogger
     */
    protected $_logger;

    protected $_isGateway = true;
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    protected $_minOrderTotal = 0;
    protected $_supportedCurrencyCodes = ['BRL'];

    /**
     * @var \Softtek\Payment\Model\Cybersource\Transaction
     */
    protected $_csTransaction;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\Request
     */
    protected $_remoteAddress;

    /**
     * @var \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface
     */
    protected $_transactionBuilder;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var EventManager
     */
    protected $_eventManager;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Softtek\Payment\Helper\Data
     */
    protected $_helper;
    /**
     * @var SchedulesMessagesHelper
     */
    private $scheduledMessagesHelper;
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
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $_logger
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param Cybersource\Transaction $csTransaction
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\HTTP\PhpEnvironment\Request $remoteAddress
     * @param \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Softtek\Payment\Helper\Data $helper
     * @param SchedulesMessagesHelper $schedulesMessagesHelper
     * @param ReverseTransactionsResource $reverseTransactionRepository
     * @param ReverseTransactionsFactory $reverseTransactionFactory,
     * @param TimezoneInterface $time
     * @param array $data
     */

    function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $_logger,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Softtek\Payment\Model\Cybersource\Transaction $csTransaction,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\HTTP\PhpEnvironment\Request $remoteAddress,
        \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Catalog\Model\CategoryFactory  $categoryFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Softtek\Payment\Helper\Data $helper,
        SchedulesMessagesHelper $schedulesMessagesHelper,
        ReverseTransactionsResource $reverseTransactionRepository,
        ReverseTransactionsFactory $reverseTransactionFactory,
        TimezoneInterface $time,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $_logger,
            $moduleList,
            $localeDate,
            null,
            null,
            $data
        );

        $this->_code = self::METHOD_CODE;
        $this->_csTransaction = $csTransaction;
        $this->_customerSession = $customerSession;
        $this->_remoteAddress = $remoteAddress;
        $this->_transactionBuilder = $transactionBuilder;
        $this->_minOrderTotal = $this->getConfigData('min_order_total');
        $this->_eventManager = $eventManager;
        $this->_categoryFactory = $categoryFactory;
        $this->_orderFactory = $orderFactory;
        $this->_helper = $helper;
        $this->scheduledMessagesHelper = $schedulesMessagesHelper;
        $this->reverseTransactionFactory = $reverseTransactionFactory;
        $this->reverseTransactionRepository = $reverseTransactionRepository;
        $this->time = $time;
    }

    /**
     * Assign data to info model instance
     *
     * @param DataObject $data
     * @return $this
     *
     */
    public function assignData(\Magento\Framework\DataObject $data)
    {
        parent::assignData($data);
        $this->getInfoInstance()->setAdditionalInformation('post_data_value', $data->getData());

        $this->_eventManager->dispatch(
            'payment_method_assign_data_' . $this->getCode(),
            [
                'method' => $this,
                'payment_model' => $this->getInfoInstance(),
                'data' => $data
            ]
        );

        $this->_eventManager->dispatch(
            'payment_method_assign_data',
            [
                'method' => $this,
                'payment_model' => $this->getInfoInstance(),
                'data' => $data
            ]
        );
        return $this;
    }

    /**
     * Validate if the currency code is supported
     *
     * @param  string $currencyCode
     * @return boolean
     */
    public function canUseForCurrency($currencyCode)
    {
        if (!in_array($currencyCode, $this->_supportedCurrencyCodes)) {
            return false;
        }
        return true;
    }

    /**
     * Set Valid CCtype for Cybersource
     *
     * @param  string $ccType
     * @return string
     */
    protected function getCcType($ccType)
    {
        $cardType = '000';

        if ($ccType == 'VI') {
            $cardType = 'visa';
        } else if ($ccType == 'MC') {
            $cardType = 'mastercard';
        } else if ($ccType == 'AE') {
            $cardType = 'american express';
        } elseif ($ccType == 'JCB') {
            $cardType = 'jcb';
        }

        return $cardType;
    }

    /**
     * Set Valid CCtype for Decision Manager
     *
     * @param  string $ccType
     * @return string
     */
    protected function getCcType2($ccType)
    {
        $cardType2 = '000';

        if ($ccType == 'VI') {
            $cardType2 = '001';
        } else if ($ccType == 'MC') {
            $cardType2 = '002';
        } else if ($ccType == 'AE') {
            $cardType2 = '003';
        } elseif ($ccType == 'JCB') {
            $cardType2 = '007';
        }
        return $cardType2;
    }

    /**
     * Get total number of days between two specified dates.
     *
     * @param  date $startDate
     * @param  date $endDate
     * @return int  $difference
     */
    protected function getDateDifference($startDate, $endDate)
    {
        $start_date = strtotime($startDate);
        $end_date = strtotime($endDate);
        $difference = ($end_date - $start_date) / 60 / 60 / 24;

        return $difference;
    }

    /**
     * Get Avarage Shipping amount for the last 6 months.
     *
     * @param Object $orders
     * @return double $totalAmount
     */
    protected function getAvarageShipping($orders)
    {
        $todayDate = date("Y-m-d");
        $totalAmout = 0;
        $orderCount = 1;
        foreach ($orders as $order) {
            $saleDate = date("Y-m-d", strtotime($order->getCreatedAt()));
            $grandTotal = (float)$order->getBaseGrandTotal();
            $dateDifference = $this->getDateDifference($saleDate, $todayDate);
            if ($dateDifference < 180) {
                $totalAmout += $grandTotal;
                $orderCount += 1;
            }
        }
        $totalAmount = $totalAmout / $orderCount;
        return $totalAmount;
    }

    /**
     * Find out if the user updated their profile
     *
     * @param date $startDate
     * @param date $udpateDate
     * @return String
     */
    protected function getUpdateProfile($startDate, $udpateDate)
    {
        $to_time = strtotime($startDate);
        $from_time = strtotime($udpateDate);
        $timeDif = round(abs($to_time - $from_time) / 60, 2);
        if ($timeDif > 1) {
            return 'Yes';
        } else {
            return 'No';
        }
    }

    /**
     * Get total number of days between two specified dates.
     * @param  String $orders
     * @param  float $amountsale
     * @return float  $orders
     */
    protected function getFactorValue($orders, $amountsale)
    {
        $today = date("Y-m-d");
        $totalAmount = 0;
        foreach ($orders as $order) {
            $saleDate = date('Y-m-d', strtotime($order->getCreatedAt()));
            $amount = (float)$order->getBaseGrandTotal();
            $diference = $this->getDateDifference($saleDate, $today);
            if ($diference < 180) {
                $totalAmount = $totalAmount + $amount;
            }
        }
        $factordes = $totalAmount / $amountsale;
        return $factordes;
    }

    /**
     * Get total number of days between two specified dates.
     * @param  String $orders
     * @return float  $orders
     */
    protected function getHistory($orders)
    {
        $today = date("Y-m-d");
        $count = 0;
        foreach ($orders as $order) {
            $saleDate = date('Y-m-d', strtotime($order->getCreatedAt()));
            $diference = $this->getDateDifference($saleDate, $today);
            if ($diference < 60) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Get total number of days between two specified dates.
     *
     * @param  String $orders
     * @return date $lastBuy
     */
    protected function getDaysOfFirstBuy($orders)
    {
        $arrayTemp = array();
        foreach ($orders as $order) {
            $datenow =  date('Y-m-d', strtotime($order->getCreatedAt()));
            array_push($arrayTemp, $datenow);
        }
        if($arrayTemp == null){
            return date("Y-m-d");
        }
        $firstDay =  $arrayTemp[0];
        return $firstDay;
    }

    /**
     * Get total number of days between two specified dates.
     *
     * @param  Strings $orders
     * @return date  $lastBuy
     */
    protected function getDaysOfLastBuy($orders)
    {
        $lastBuy = null;
        foreach ($orders as $order) {
            $lastBuy =  date('Y-m-d', strtotime($order->getCreatedAt()));
        }
        return $lastBuy;
    }

    /**
     * Get only simple products from cart.
     *
     * @param  Array $orders
     * @return Array  $lastBuy
     */
    protected function getSimpleProducts($items)
    {
        $products = array();
        for ($i = 0; $i < sizeof($items); $i++) {
            if($items[$i]->getProductType() == 'simple') {
                array_push($products, $items[$i]) ;
            }
        }
        return $products;
    }

    /**
     * Capture the payment (Auto Authorize and Capture)
     *
     * @param  MagentoPaymentModelInfoInterface $payment
     * @param  int                              $amount
     * @return $this
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $orders = $this->_orderFactory->create()->getCollection()->addFieldToFilter('customer_email', $this->_customerSession->getCustomer()->getEmail());
        $info = $this->getInfoInstance();
        $order = $payment->getOrder();
        $billing = $order->getBillingAddress();
        $shipping = $order->getShippingAddress();
        $items = $this->getSimpleProducts($order->getItems());
        $cardType = $this->getCcType($payment->getCcType());
        $cardType2 = $this->getCcType2($payment->getCcType());
        $ip = $this->_remoteAddress->getClientIp();
        $userId = "notLogged";
        $userDob = "00000000";
        $dayDifference = 'notApply';
        $isLoggedIn = $this->_customerSession->isLoggedIn();
        $fingerPrint = $payment->getAdditionalInformation('post_data_value')['additional_data']['finger'];
        $cardPasted = "No";
        $trys = $payment->getAdditionalInformation('post_data_value')['additional_data']['trys'];
        $cardChanged = "No";
        $categories = null;
        $updateProfile = 'notApply';
        $updateDaysDiff = 'notApply';
        $avarageShippingAmount = 'notApply';
        $factorValue = 0;
        $dayOfFirstBuy = 'notApply';
        $dayOfLastBuy = 'notApply';
        $history = "notApply";

        if ($isLoggedIn) {
            $userId = strval($this->_customerSession->getCustomer()->getId());
            $userDob = strval(str_replace("-", "", $this->_customerSession->getCustomer()->getDob()));
            $startDate = date('Y-m-d', strtotime($this->_customerSession->getCustomer()->getCreatedAt()));
            $endDate = date("Y-m-d");
            $dayDifference = $this->getDateDifference($startDate, $endDate);
            $udpateDate = date('Y-m-d', strtotime($this->_customerSession->getCustomer()->getUpdatedAt()));
            $updateDaysDiff = $this->getDateDifference($startDate, $udpateDate);
            $updateProfile = $this->getUpdateProfile($this->_customerSession->getCustomer()->getCreatedAt(), $this->_customerSession->getCustomer()->getUpdatedAt());
            $orders = $this->_orderFactory->create()->getCollection()->addFieldToFilter('customer_email', $this->_customerSession->getCustomer()->getEmail());
            $avarageShippingAmount = round($this->getAvarageShipping($orders), 2);
            $factorValue = $this->getFactorValue($orders, $amount);
            $dayOfFirstBuy = $this->getDateDifference($this->getDaysOfFirstBuy($orders), $endDate);
            $dayOfLastBuy = $this->getDateDifference($this->getDaysOfLastBuy($orders), $endDate);
            $history = $this->getHistory($orders);
        }

        if (($payment->getAdditionalInformation('post_data_value')['additional_data']['card_changed']) == 1) {
            $cardChanged = "Si";
        }

        if (($payment->getAdditionalInformation('post_data_value')['additional_data']['card_pasted']) == 1) {
            $cardPasted = "Si";
        }

        foreach ($items as $item) {
            $flag = false;
            $itema = $item->getProduct()->getData();

            $this->_logger->debug(json_encode([
                'itemData' => $itema
            ]));

            $arrayCategories = array();
            foreach ($itema['category_ids'] as $categoryId) {
                array_push($arrayCategories, $categoryId);
            }
        }

        // TODO: Get Seller ID basse on cart products to obtain user_id, terminal_id, merchant_id from purpletree_marketplace_stores table
        // Aquí la idea es teniendo el producto (item) conocer cual es su seller, teniendo su id se puede acceder al la collection y de ahí se sacan los datos

        $arrayunique = array_unique($arrayCategories);

        foreach ($arrayunique as $catIds) {
            $category = $this->_categoryFactory->create()->load($catIds);
            $categories = $categories . $category->getName() . " , ";
        }

        $incrementId = $order->getIncrementId();
        $currencyCode = $order->getBaseCurrencyCode();

        try {
            $transactionResponse = $this->_csTransaction->createTransaction([
                'code' => $incrementId,
                'transactionId' => $incrementId,
                'lineItems' => $items,
                'shippingMethod' => $order->getShippingMethod(),
                'card' => [
                    'number' => $payment->getCcNumber(),
                    'expirationMonth' => sprintf('%02d', $payment->getCcExpMonth()),
                    'expirationYear' => $payment->getCcExpYear(),
                    'cvv' => $payment->getCcCid(),
                    'type' => $cardType,
                    'type2' => $cardType2,
                ],
                'currency' => $currencyCode,
                'totalAmount' => $amount,
                'billingAddress' => [
                    'address1' => $billing->getStreetLine(1),
                    'address2' => $billing->getStreetLine(2),
                    'buildingNumber' => $billing->getBuildingNumber(),
                    'administrativeArea' => $billing->getRegionCode(),
                    'country' => $billing->getCountryId(),
                    'locality' => $billing->getCity(),
                    'firstName' => $billing->getFirstName(),
                    'lastName' => $billing->getLastName(),
                    'phoneNumber' => $billing->getTelephone(),
                    'email' => $billing->getEmail(),
                    'postalCode' => $billing->getPostcode(),
                    'company' => $billing->getCompany(),
                ],
                'shippingAddress' => [
                    'address1' => $shipping->getStreetLine(1),
                    'address2' => $shipping->getStreetLine(2),
                    'buildingNumber' => $shipping->getBuildingNumber(),
                    'administrativeArea' => $shipping->getRegionCode(),
                    'country' => $shipping->getCountryId(),
                    'locality' => $shipping->getCity(),
                    'firstName' => $shipping->getFirstName(),
                    'lastName' => $shipping->getLastName(),
                    'phoneNumber' => $shipping->getTelephone(),
                    'email' => $shipping->getEmail(),
                    'postalCode' => $shipping->getPostcode(),
                ],
                'customer' => [
                    'ip' => $ip,
                    'userId' => $userId,
                    'userDob' => $userDob,
                    'fingerPrint' => $fingerPrint
                ],
                'MDD' => [
                    'salesChannel' => 'Web Channel',
                    'productCategory' => $categories,
                    'loggedIn' => strval($isLoggedIn),
                    'antiquity' => strval($dayDifference),
                    'commerce' => $this->_helper->getMerchantId(),
                    'segment' => 'Retail',
                    'installments' => '0',
                    'clientDocument' => 'notApply',
                    'purchaseAvarage' => $avarageShippingAmount,
                    'customerDataChange' => $updateProfile,
                    'daysCustomerDataC' => $updateDaysDiff,
                    'factorValue' => strval($factorValue),
                    'daysFirstP' => $dayOfFirstBuy,
                    'daysLastP' => $dayOfLastBuy,
                    'shoppingHistory' => strval($history),
                    'personalLoan' => 'No',
                    'cardPasted' => $cardPasted,
                    'trys' => $trys,
                    'cardChanged' => $cardChanged,
                ],
            ]);

            $transactionId = $transactionResponse->getId();
            $transactionStatus = $transactionResponse->getStatus();
            $transactionCode = $transactionResponse->getProcessorInformation()->getApprovalCode();
            $orderEmail = $order->getCustomerEmail();

            $transactionReverse = false;
            if ($transactionStatus == $this::AUTHORIZED_STATUS) {
                $transactionReverse = $this->_saveForReverse($incrementId, $transactionId,
                    $this::METHOD_CODE, $orderEmail, $amount, $currencyCode, $transactionStatus,
                    $transactionCode);
            }

            $response_data = [
                'requestId' => $transactionId,
                'buyOrder' => $transactionResponse->getClientReferenceInformation()->getCode(),
                'transactionDate' => $transactionResponse->getSubmitTimeUtc(),
                'status' => $transactionStatus,
                'amount' => $transactionResponse->getOrderInformation()->getAmountDetails()->getAuthorizedAmount(),
                'currency' => $transactionResponse->getOrderInformation()->getAmountDetails()->getCurrency(),
                'authorization_code' => $transactionResponse->getProcessorInformation()->getApprovalCode(),
                'response_code' => $transactionResponse->getProcessorInformation()->getResponseCode(),
                'buy_order' => $transactionResponse->getClientReferenceInformation()->getCode(),
                'reconciliationId' => $transactionResponse->getReconciliationId(),
                'transactionId' => $transactionResponse->getProcessorInformation()->getTransactionId(),
            ];

            $charge_id = $transactionResponse->getId();
            $payment->setTransactionId($charge_id)->setIsTransactionClosed(0);
            $payment->setAdditionalInformation(
                [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $response_data]
            );
            $formatedPrice = $order->getBaseCurrency()->formatTxt(
                $order->getGrandTotal()
            );
            $message = __('Cybersource: O total autorizado é %1.', $formatedPrice);

            // Get the object of builder class
            $trans_builder = $this->_transactionBuilder;
            $transaction = $trans_builder->setPayment($payment)
                ->setOrder($order)
                ->setTransactionId($order->getIncrementId())
                ->setAdditionalInformation(
                    [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $response_data]
                )
                ->setFailSafe(true)
                ->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE);
            $payment->addTransactionCommentsToOrder($transaction, $message);
            $payment->setParentTransactionId(null);
            $order->save();
            $transaction->save();

            if ($transactionReverse) {
                $transactionReverse->setStatus(ReverseTransactionsStatusName::processed);
                $transactionReverse->setIsProcessed(true);
                $transactionReverse->setProcessedDate($transaction->getCreatedAt());
                $this->_updateReverseTransaction($transactionReverse);
            }

            $this->scheduledMessagesHelper->saveMessage($order);

            return $this;
        } catch (\Exception $e) {
            $this->_logger->debug('exception' . $e->getMessage());
            $this->debugData(['exception' => $e->getMessage()]);
            throw new \Magento\Framework\Validator\Exception(__('Um erro ocorreu no serviço de pagamentos, verifique os dados e tente novamente mais tarde.'));
        }
    }

    /**
     * Refund the payment and close transaction
     *
     * @param  MagentoPaymentModelInfoInterface $payment
     * @param  int                              $amount
     * @return $this
     */
    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $order = $payment->getOrder();
        $transactionId = $payment->getParentTransactionId();

        try {
            $refundResponse = $this->_csTransaction->processRefund([
                'code' => $order->getIncrementId(),
                'transactionId' => $transactionId,
                'currency' => $order->getBaseCurrencyCode(),
                'totalAmount' => $amount,
            ]);

            $this->_logger->debug('Refund Transaction Status Result: ' . $refundResponse->getStatus());
            $this->_logger->debug('Refund Transaction Id: ' . $refundResponse->getId());

            $this->scheduledMessagesHelper->saveRemCancelacionMessage($order);

            return $this;
        } catch (\Exception $e) {
            $this->debugData(['transaction_id' => $transactionId, 'exception' => $e->getMessage()]);
            throw new \Magento\Framework\Validator\Exception(__('Não foi possível gerar o processo de devolução. Tentar novamente mais tarde. '));
        }
    }

    /**
     * Validate if the minimun order total is OK
     *
     * @param  MagentoQuoteApiDataCartInterface  $quote
     * @return boolean
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        $this->_minOrderTotal = $this->getConfigData('min_order_total');

        if ($quote && $quote->getBaseGrandTotal() < $this->_minOrderTotal) {
            return false;
        }

        return parent::isAvailable($quote);
    }

    /**
     * Get list of credit cards verification reg exp.
     *
     * @return array
     * @api
     */
    public function getVerificationRegEx()
    {
        $verificationExpList = [
            'VI' => '/^[0-9]{3}$/',
            'MC' => '/^[0-9]{3}$/',
            'AE' => '/^[0-9]{3,4}$/',
            'DI' => '/^[0-9]{3}$/',
            'DN' => '/^[0-9]{3}$/',
            'UN' => '/^[0-9]{3}$/',
            'SS' => '/^[0-9]{3,4}$/',
            'SM' => '/^[0-9]{3,4}$/',
            'SO' => '/^[0-9]{3,4}$/',
            'OT' => '/^[0-9]{3,4}$/',
            'JCB' => '/^[0-9]{3,4}$/',
            'MI' => '/^[0-9]{3}$/',
            'MD' => '/^[0-9]{3}$/',
        ];
        return $verificationExpList;
    }

    /**
     * Save transaction in Reverse Transactions
     *
     * @param $incrementId
     * @param $transactionId
     * @param $paymentMethod
     * @param $orderEmail
     * @param $amount
     * @param $currencyCode
     * @param $status
     * @param $message
     * @return ReverseTransactions
     */
    protected function _saveForReverse($incrementId, $transactionId, $paymentMethod, $orderEmail,
        $amount, $currencyCode, $status, $message)
    {
        $data = [
            'customer_email' => $orderEmail,
            'increment_id' => $incrementId,
            'transaction_id' => $transactionId,
            'transaction_date' => $this->time->date()->format('Y-m-d h:i:s'),
            'amount' => $amount,
            'currency_code' => $currencyCode,
            'status' => $status,
            'payment_method' => $paymentMethod,
            'reverse_error_details' => $message
        ];

        $newTransaction = $this->reverseTransactionFactory->create();
        try {
            $this->reverseTransactionRepository->save($newTransaction->addData($data));
            return $newTransaction;
        } catch (CouldNotSaveException $e) {
            $this->_logger->error("Error when saving ReverseTransaction" .
                " for order number: " . $incrementId . " Message: " . $e->getMessage());
            return null;
        }
    }

    /**
     * @param $reverseTransaction
     * @return void |null
     */
    protected function _updateReverseTransaction($reverseTransaction)
    {
        try {
            $this->reverseTransactionRepository->save($reverseTransaction);
            return $reverseTransaction;
        } catch (CouldNotSaveException $e) {
            $this->_logger->error("Error when updating ReverseTransaction with ID: " .
            $reverseTransaction->getId() . " for order number: " .
            $reverseTransaction->getIncrementId() . " " .
                $e->getMessage());
            return null;
        }
    }
    public function validate(){
        $errorMsg = false;
        $info = $this->getInfoInstance();
        $ccType = '';

        if ($ccType == 'SS' && !$this->_validateExpDate($info->getCcExpYear(), $info->getCcExpMonth())) {
            $errorMsg = __('Please enter a valid credit card expiration datesss.');
        }
        if ($errorMsg) {
            throw new \Magento\Framework\Exception\LocalizedException($errorMsg);
        }
    }
}
