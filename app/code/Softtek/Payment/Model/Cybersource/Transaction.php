<?php

/**
 * Config Provider Class
 *
 * @package Softtek_Payment
 * @author Paul Soberanes <paul.soberanes@softtek.com>, Jorge Serena  <jorge.serena@softtek.com>
 * @copyright © Softtek. All rights reserved.
 */

namespace Softtek\Payment\Model\Cybersource;

class Transaction extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Cybersource Merchant Configuration
     */
    protected $authType = "http_signature"; //http_signature/jwt
    protected $enableLog = true;
    protected $logSize = "1048576";
    protected $logFile = "var/log";
    protected $logFilename = "cybs.log";
    protected $keyAlias = "testrest";
    protected $keyPass = "testrest";
    protected $keyFilename = "testrest";
    protected $keyDirectory = "Resources/";
    protected $runEnv = "cyberSource.environment";

    protected $_logger;
    protected $_scopeConfig;
    protected $_helper;
    protected $_dir;

    /**
     * @param PsrLogLoggerInterface                           $logger
     * @param MagentoFrameworkAppConfigScopeConfigInterface   $scopeConfig
     * @param MagentoPaymentHelperData                        $helper
     * @param MagentoFrameworkFilesystemDirctoryList          $dir
     */

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Softtek\Payment\Helper\Data $helper,
        \Magento\Framework\Filesystem\DirectoryList $dir
    ) {
        $this->_logger = $logger;
        $this->_scopeConfig = $scopeConfig;
        $this->_helper = $helper;
        $this->_dir = $dir;
    }


    /**
     * Create Cybersource Transaction workflow
     *
     * @param array $data
     * @return object
     */
    public function createTransaction($data) {
        $instrumentIdentifier = $this->createInstrumentIdentifierCard($data);
        $paymentInstrument = $this->createPaymentInstrumentCard($data, $instrumentIdentifier->getId());
        $transactionDM = $this->callDecisionManager($data, $paymentInstrument->getId());

        try {
            if($transactionDM->getStatus() == 'ACCEPTED') {
                $transactionResponse = $this->authorizationAndCaptureSale($data, $paymentInstrument->getId());
                return $transactionResponse;
            } else {
                throw new \Magento\Framework\Validator\Exception(__('Erro da API de pagamento: A transação foi encerrada. Por favor tentar novamente mais tarde.'));
            }
        } catch (\Cybersource\ApiException $e) {
            $this->_logger->debug(print_r($e->getMessage()));
            throw new \Magento\Framework\Validator\Exception(__('Erro no pagamento: A transação foi encerrada. Por favor tentar novamente mais tarde.'));
        }
    }
    /**
     * Capture card authorization and payment
     *
     * @param array $data
     * @param string $paymentInstrumentId
     * @return object
     */
    public function authorizationAndCaptureSale($data, $paymentInstrumentId)
    {
        $clientReferenceInformation = new \CyberSource\Model\Ptsv2paymentsClientReferenceInformation([
            "code" => $data['code']
        ]);

        $paymentInformationInstrument = new \CyberSource\Model\Ptsv2paymentsPaymentInformationPaymentInstrument([
            "id" => $paymentInstrumentId
        ]);

        $paymentInformationCard = new \CyberSource\Model\Ptsv2paymentsPaymentInformationCard([
            'securityCode' => $data['card']['cvv'],
            "type" => $data['card']['type2'],
        ]);

        $paymentInformation = new \CyberSource\Model\Ptsv2paymentsPaymentInformation([
            "card" => $paymentInformationCard,
            "paymentInstrument" => $paymentInformationInstrument
        ]);

        $orderInformationAmountDetails = new \CyberSource\Model\Ptsv2paymentsOrderInformationAmountDetails([
            'totalAmount' => $data['totalAmount'],
            'currency' => $data['currency'],
        ]);

        $orderInformationBillTo = new \CyberSource\Model\Ptsv2paymentsOrderInformationBillTo([
            'firstName' => $data['billingAddress']['firstName'],
            'lastName' => $data['billingAddress']['lastName'],
            'address1' => $data['billingAddress']['address1'],
            'address2' => $data['billingAddress']['address2'],
            'buildingNumber' => $data['billingAddress']['buildingNumber'],
            'locality' => $data['billingAddress']['locality'],
            'administrativeArea' => $data['billingAddress']['administrativeArea'],
            'postalCode' => $data['billingAddress']['postalCode'],
            'country' => $data['billingAddress']['country'],
            'email' => $data['billingAddress']['email'],
            'phoneNumber' => $data['billingAddress']['phoneNumber'],
        ]);

        $orderInformation = new \CyberSource\Model\Ptsv2paymentsOrderInformation([
            "amountDetails" => $orderInformationAmountDetails,
            "billTo" => $orderInformationBillTo,
        ]);

        $authorizationOptionsInitiator = new \CyberSource\Model\Ptsv2paymentsProcessingInformationAuthorizationOptionsInitiator([
            "storedCredentialUsed" => false,
        ]);

        $authorizationOptions = new \CyberSource\Model\Ptsv2paymentsProcessingInformationAuthorizationOptions([
            "initiator" => $authorizationOptionsInitiator,
        ]);

        $processingInformation = new \CyberSource\Model\Ptsv2paymentsProcessingInformation([
            "capture" => true,
            "commerceIndicator" => "internet",
            "authorizationOptions" => $authorizationOptions,
            "actionList" => ["DECISION_SKIP"]
        ]);

        $requestObjArr = [
                "clientReferenceInformation" => $clientReferenceInformation,
                "processingInformation" => $processingInformation,
                "paymentInformation" => $paymentInformation,
                "orderInformation" => $orderInformation,
        ];
        $requestObj = new \CyberSource\Model\CreatePaymentRequest($requestObjArr);

        $config = $this->ConnectionHost();
        $merchantConfig = $this->merchantConfigObject();

        $api_client = new \CyberSource\ApiClient($config, $merchantConfig);
        $api_instance = new \CyberSource\Api\PaymentsApi($api_client);

        try {
            $apiResponse = $api_instance->createPayment($requestObj);
            $apiResponseObj = current($apiResponse);

            // $this->_logger->debug('Api Response: ' . json_encode($apiResponse));
            // $this->_logger->debug('Api Response Object ' . $apiResponseObj);

            return $apiResponseObj;
        } catch (\Cybersource\ApiException $e) {
            $this->_logger->debug(print_r($e->getMessage()));
            throw new \Magento\Framework\Validator\Exception(__('Payment API error: Error en la transacción.'));
        }
    }

    /**
     * Capture Decision Manager
     *
     * @param array $data
     * @param string $paymentInstrumentId
     * @return object
     */
    public function callDecisionManager($data, $paymentInstrumentId)
    {
        $clientReferenceInformation = new \CyberSource\Model\Riskv1decisionsClientReferenceInformation([
            "code" => $data['code']
        ]);

        $paymentInformationInstrument = new \CyberSource\Model\Ptsv2paymentsPaymentInformationCustomer([
            "customerId" => $paymentInstrumentId
        ]);

        $paymentInformation = new \CyberSource\Model\Riskv1decisionsPaymentInformation([
            "customer" => $paymentInformationInstrument
        ]);

        $orderInformationAmountDetails = new \CyberSource\Model\Riskv1decisionsOrderInformationAmountDetails([
            'totalAmount' => $data['totalAmount'],
            'currency' => $data['currency'],
        ]);

        $orderInformationBillTo = new \CyberSource\Model\Riskv1decisionsOrderInformationBillTo([
            'firstName' => $data['billingAddress']['firstName'],
            'lastName' => $data['billingAddress']['lastName'],
            'address1' => $data['billingAddress']['address1'],
            'address2' => $data['billingAddress']['address2'],
            'buildingNumber' => $data['billingAddress']['buildingNumber'],
            'locality' => $data['billingAddress']['locality'],
            'administrativeArea' => $data['billingAddress']['administrativeArea'],
            'postalCode' => $data['billingAddress']['postalCode'],
            'country' => $data['billingAddress']['country'],
            'email' => $data['billingAddress']['email'],
            'phoneNumber' => $data['billingAddress']['phoneNumber'],
        ]);

        $orderInformationShipTo = new \CyberSource\Model\Riskv1decisionsOrderInformationShipTo([
            'address1' => $data['shippingAddress']['address1'],
            'address2' => $data['shippingAddress']['address2'],
            'buildingNumber' => $data['shippingAddress']['buildingNumber'],
            'administrativeArea' => $data['shippingAddress']['administrativeArea'],
            'country' => $data['shippingAddress']['country'],
            'locality' => $data['shippingAddress']['locality'],
            'firstName' => $data['shippingAddress']['firstName'],
            'lastName' => $data['shippingAddress']['lastName'],
            'phoneNumber' => $data['shippingAddress']['phoneNumber'],
            'email' => $data['shippingAddress']['email'],
            'postalCode' => $data['shippingAddress']['postalCode'],
        ]);

        $items = $data['lineItems'];
        $orderInformationLineItems = array();

        for ($i = 0; $i < sizeof($items); $i++) {
            $orderInformationLineItems[$i] = new \CyberSource\Model\Riskv1decisionsOrderInformationLineItems([
                "unitPrice" => strval($items[$i]->getPrice()),
                "quantity" => $items[$i]->getqty_ordered(),
                "productSKU" => $items[$i]->getSku(),
                "productName" => $items[$i]->getName(),
                "productCode" => 'default',
            ]);
        }

        $orderInformation = new \CyberSource\Model\Riskv1decisionsOrderInformation([
            "amountDetails" => $orderInformationAmountDetails,
            "billTo" => $orderInformationBillTo,
            "shipTo" => $orderInformationShipTo,
            "lineItems" => $orderInformationLineItems
        ]);

        $buyerInformation = new \CyberSource\Model\Riskv1decisionsBuyerInformation([
            "dateOfBirth" => $data['customer']['userDob'],
        ]);

        $deviceInformation = new \CyberSource\Model\Riskv1decisionsDeviceInformation([
            "ipAddress" => $data['customer']['ip'],
            "fingerprintSessionId" => $data['customer']['fingerPrint'],
        ]);

        $mdds = $data['MDD'];
        $keys = array_keys($mdds);
        $merchantDefinedInformation = array();

        for ($j = 0; $j < sizeof($keys); $j++) {
            $merchantDefinedInformation[$j] = new \CyberSource\Model\Riskv1decisionsMerchantDefinedInformation([
                'key' => $j + 1,
                'value' =>  $mdds[$keys[$j]]
            ]);
        }

        $requestObj = new \CyberSource\Model\CreateBundledDecisionManagerCaseRequest([
            "clientReferenceInformation" => $clientReferenceInformation,
            "paymentInformation" => $paymentInformation,
            "orderInformation" => $orderInformation,
            "buyerInformation" => $buyerInformation,
            "deviceInformation" => $deviceInformation,
            "merchantDefinedInformation" => $merchantDefinedInformation
        ]);

        $config = $this->ConnectionHost();
        $merchantConfig = $this->merchantConfigObject();

        $api_client = new \CyberSource\ApiClient($config, $merchantConfig);
        $api_instance = new \CyberSource\Api\DecisionManagerApi($api_client);

        try {
            $apiResponse = $api_instance->createBundledDecisionManagerCase($requestObj);
            $apiResponseObj = current($apiResponse);

            // $this->_logger->debug('Api Response: ' . json_encode($apiResponse));
            // $this->_logger->debug('Api Response Object ' . $apiResponseObj);

            return $apiResponseObj;
        } catch (\Cybersource\ApiException $e) {
            $this->_logger->debug(print_r($e->getMessage()));
            throw new \Magento\Framework\Validator\Exception(__('Payment API error: Error en la transacción.'));
        }
    }

    /**
     * Create Payment Instrument in cybersource service.
     *
     * @param array $data
     * @param string $instrumentIdentifierId
     * @return object
     */
    public function createPaymentInstrumentCard($data, $instrumentIdentifierId)
    {
        $profileid = $this->_helper->getProfileId();

        $card = new \CyberSource\Model\Tmsv2customersEmbeddedDefaultPaymentInstrumentCard([
            "expirationMonth" => $data['card']['expirationMonth'],
            "expirationYear" => $data['card']['expirationYear'],
            "type" => $data['card']['type'],
        ]);

        $billTo = new \CyberSource\Model\Tmsv2customersEmbeddedDefaultPaymentInstrumentBillTo([
            'firstName' => $data['billingAddress']['firstName'],
            'lastName' => $data['billingAddress']['lastName'],
            'company' => $data['billingAddress']['company'],
            'address1' => $data['billingAddress']['address1'],
            'locality' => $data['billingAddress']['locality'],
            'administrativeArea' => $data['billingAddress']['administrativeArea'],
            'postalCode' => $data['billingAddress']['postalCode'],
            'country' => $data['billingAddress']['country'],
            'email' => $data['billingAddress']['email'],
            'phoneNumber' => $data['billingAddress']['phoneNumber'],
        ]);

        $instrumentIdentifier = new \CyberSource\Model\Tmsv2customersEmbeddedDefaultPaymentInstrumentInstrumentIdentifier([
            "id" => $instrumentIdentifierId
        ]);

        $requestObj = new \CyberSource\Model\PostPaymentInstrumentRequest([
            "card" => $card,
            "billTo" => $billTo,
            "instrumentIdentifier" => $instrumentIdentifier
        ]);

        $config = $this->ConnectionHost();
        $merchantConfig = $this->merchantConfigObject();

        $api_client = new \CyberSource\ApiClient($config, $merchantConfig);
        $api_instance = new \CyberSource\Api\PaymentInstrumentApi($api_client);

        try {
            $apiResponse = $api_instance->postPaymentInstrument($requestObj, $profileid);
            $apiResponseObj = current($apiResponse);

            return $apiResponseObj;
        } catch (\Cybersource\ApiException $e) {
            $this->_logger->debug(print_r($e->getMessage()));
            throw new \Magento\Framework\Validator\Exception(__('Payment API error: Error en la transacción.'));
        }
    }

    /**
     * Create Instrument Identifier token to represent the tokenized Primary Account Number (PAN)
     * for card payments in cybersource service.
     *
     * @param array $data
     * @return object
     */
    public function createInstrumentIdentifierCard($data)
    {
        $profileid = $this->_helper->getProfileId();

        $card = new \CyberSource\Model\Tmsv2customersEmbeddedDefaultPaymentInstrumentEmbeddedInstrumentIdentifierCard([
            'number' => $data['card']['number']
        ]);

        $requestObj = new \CyberSource\Model\PostInstrumentIdentifierRequest([
            "card" => $card
        ]);

        $config = $this->ConnectionHost();
        $merchantConfig = $this->merchantConfigObject();

        $api_client = new \CyberSource\ApiClient($config, $merchantConfig);
        $api_instance = new \CyberSource\Api\InstrumentIdentifierApi($api_client);

        try {
            $apiResponse = $api_instance->postInstrumentIdentifier($requestObj, $profileid);
            $apiResponseObj = current($apiResponse);

            return $apiResponseObj;
        } catch (\Cybersource\ApiException $e) {
            $this->_logger->debug(print_r($e->getMessage()));
            throw new \Magento\Framework\Validator\Exception(__('Payment API error: Error en la transacción.'));
        }
    }

    /**
     * Refund PAyment Capture in cybersource service.
     * @param array $data
     * @return object
     */
    public function processRefund($data)
    {
        $id = $data['transactionId'];

        $clientReferenceInformation = new \CyberSource\Model\Ptsv2paymentsClientReferenceInformation([
            "code" => $data['code']
        ]);

        $orderInformationAmountDetails = new \CyberSource\Model\Ptsv2paymentsidcapturesOrderInformationAmountDetails([
            'totalAmount' => $data['totalAmount'],
            'currency' => $data['currency'],
        ]);

        $orderInformation = new \CyberSource\Model\Ptsv2paymentsidrefundsOrderInformation([
            "amountDetails" => $orderInformationAmountDetails
        ]);

        $requestObjArr = [
            "clientReferenceInformation" => $clientReferenceInformation,
            "orderInformation" => $orderInformation
        ];
        $requestObj = new \CyberSource\Model\RefundCaptureRequest($requestObjArr);

        $config = $this->ConnectionHost();
        $merchantConfig = $this->merchantConfigObject();

        $api_client = new \CyberSource\ApiClient($config, $merchantConfig);
        $api_instance = new \CyberSource\Api\RefundApi($api_client);

        // $this->_logger->debug('Api Request Object ' . $requestObj);

        try {
            $apiResponse = $api_instance->refundPayment($requestObj, $id);
            $apiResponseObj = current($apiResponse);

            // $this->_logger->debug('Api Response Object ' . $apiResponseObj);

            return $apiResponseObj;
        } catch (\Cybersource\ApiException $e) {
            $this->_logger->debug(print_r($e->getMessage()));
            throw new \Magento\Framework\Validator\Exception(__('Payment API error: Error en la transacción de devolución.'));
        }
    }

    /**
     * creating merchant config object
     *
     * @return CyberSourceAuthenticationCoreMerchantConfiguration
     */
    protected function merchantConfigObject()
    {

        try {
            $config = new \CyberSource\Authentication\Core\MerchantConfiguration();
            if (is_bool($this->enableLog)) {
                $confiData = $config->setDebug($this->enableLog);
            }

            $confiData = $config->setLogSize(trim($this->logSize));
            $confiData = $config->setDebugFile(trim($this->_dir->getRoot() . '/' . $this->logFile));
            $confiData = $config->setLogFileName(trim($this->logFilename));
            $confiData = $config->setauthenticationType(strtoupper(trim($this->authType)));
            $confiData = $config->setMerchantID(trim($this->_helper->getMerchantId()));
            $confiData = $config->setApiKeyID($this->_helper->getApiKey());
            $confiData = $config->setSecretKey($this->_helper->getApiSecret());
            $confiData = $config->setKeyFileName(trim($this->_helper->getKeyFileName()));
            $confiData = $config->setKeyAlias($this->_helper->getKeyAlias());
            $confiData = $config->setKeyPassword($this->_helper->getKeyPass());
            $confiData = $config->setRunEnvironment($this->getEnvioment());

            return $config;
        } catch (\Cybersource\ApiException $e) {
            $this->debugData(['exception' => $e->getMessage()]);
            throw new \Magento\Framework\Validator\Exception(__('Payment refunding error'));
        }
    }

    protected function getEnvioment()
    {
        $runEnvhelper = ".SANDBOX";
        if ($this->_helper->getRunEnv()) {
            $runEnvhelper = ".PRODUCTION";
        }
        return $this->runEnv . $runEnvhelper;
    }

    /**
     * Get the Connection and CyberSource Configuration
     *
     * @return CyberSourceConfiguration
     */
    protected function connectionHost()
    {
        $merchantConfig = $this->merchantConfigObject();

        $config = new \CyberSource\Configuration();
        $config = $config->setHost($merchantConfig->getHost());
        $config = $config->setDebug($merchantConfig->getDebug());
        $config = $config->setDebugFile($merchantConfig->getDebugFile() . DIRECTORY_SEPARATOR . $merchantConfig->getLogFileName());

        return $config;
    }
}
