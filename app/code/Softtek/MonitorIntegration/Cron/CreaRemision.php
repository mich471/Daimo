<?php
/**
 * Copyright © Softtek 2020 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Softtek\MonitorIntegration\Cron;

use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\SalesRule\Api\CouponRepositoryInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Psr\Log\LoggerInterface;
use Softtek\MonitorIntegration\Helper\ConfigHelper;
use Softtek\MonitorIntegration\Helper\SchedulesMessagesHelper;
use Softtek\MonitorIntegration\Helper\SendEmailHelper;
use Softtek\MonitorIntegration\Model\Enum\MonitorInterfacesName;
use Softtek\MonitorIntegration\Model\Enum\ScheduledMessageStatus;
use Softtek\MonitorIntegration\Model\ScheduledMessagesToMonitorRepository;
use Softtek\MonitorIntegration\Service\MonitorIntegrationService;
use Throwable;

/**
 * Class CreaRemision
 * @package Softtek\MonitorIntegration\Cron
 */
class CreaRemision
{

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * @var ProductFactory
     */
    protected $_productloader;

    /**
     * @var Image
     */
    protected $helperImport;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var false
     */
    private $requiereValidacionQF;

    /**
     * @var MonitorIntegrationService
     */
    private $service;

    /**
     * @var TimezoneInterface
     */
    private $date;

    /**
     * @var ScheduledMessagesToMonitorRepository
     */
    protected $monitorRepository;

    /**
     * @var Attachment
     */
    protected $attachmentHelper;

    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * @var SendEmailHelper
     */
    protected $mailHelper;

    /**
     * @var string
     */
    protected $clientCode;

    /**
     * @var SourceRepositoryInterface
     */
    private $sourceRepository;

    /**
     * @var SchedulesMessagesHelper
     */
    private $scheduledMessagesHelper;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var \CouponRepositoryInterface
     */
    private $couponRepository;
    /**
     * @var RuleRepositoryInterface
     */
    private $rule;

    /**
     * Constructor
     *
     * @param ObjectManagerInterface $objectManager
     * @param CollectionFactory $orderCollectionFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param ProductFactory $_productloader
     * @param Image $helperImport
     * @param ResourceConnection $resourceConnection
     * @param MonitorIntegrationService $service
     * @param TimezoneInterface $date
     * @param ScheduledMessagesToMonitorRepository $scheduledMessagesToMonitorRepository
     * @param ConfigHelper $configHelper
     * @param SendEmailHelper $mailHelper
     * @param SourceRepositoryInterface $sourceRepository
     * @param SchedulesMessagesHelper $schedulesMessagesHelper
     * @param CouponRepositoryInterface $couponRepository
     * @param RuleRepositoryInterface $rule
     * @param LoggerInterface $logger
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        CollectionFactory $orderCollectionFactory,
        OrderRepositoryInterface $orderRepository,
        ProductFactory $_productloader,
        Image $helperImport,
        ResourceConnection $resourceConnection,
        MonitorIntegrationService $service,
        TimezoneInterface $date,
        ScheduledMessagesToMonitorRepository $scheduledMessagesToMonitorRepository,
        ConfigHelper $configHelper,
        SendEmailHelper $mailHelper,
        SourceRepositoryInterface $sourceRepository,
        SchedulesMessagesHelper $schedulesMessagesHelper,
        CouponRepositoryInterface $couponRepository,
        RuleRepositoryInterface $rule,
        LoggerInterface $logger
    ) {
        date_default_timezone_set('America/Mexico_City');
        $this->_objectManager = $objectManager;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_orderRepository = $orderRepository;
        $this->_productloader = $_productloader;
        $this->helperImport = $helperImport;
        $this->resourceConnection = $resourceConnection;
        $this->service = $service;
        $this->date = $date;
        $this->monitorRepository = $scheduledMessagesToMonitorRepository;
        $this->configHelper = $configHelper;
        $this->mailHelper = $mailHelper;
        $this->sourceRepository = $sourceRepository;
        $this->scheduledMessagesHelper = $schedulesMessagesHelper;
        $this->couponRepository = $couponRepository;
        $this->rule = $rule;
        $this->logger = $logger;
        $this->requiereValidacionQF = false;
        $this->clientCode = strtoupper($this->configHelper->getClientCode());
    }

    /**
     * Execute the cron
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $this->logger->info("Cronjob CreaRemision is starting.");
        $searchCriteriaBuilder = $this->_objectManager->create('Magento\Framework\Api\SearchCriteriaBuilder');
        $scheduledMessagesRepository = $this->_objectManager->get(ScheduledMessagesToMonitorRepository::class);

        $ordersSearch = $searchCriteriaBuilder
            ->addFilter('monitor_interface', MonitorInterfacesName::N1, 'eq')
            ->addFilter('status', ScheduledMessageStatus::PENDING, 'eq')
            ->addFilter('number_of_retries', 2, 'lt')
            ->create();

        $scheduledMessages = $scheduledMessagesRepository->getList($ordersSearch)->getItems();

        foreach ($scheduledMessages as $scheduledMessage) {
            $this->logger->debug("Creating remision for order " . json_encode($scheduledMessage->getOrderId()));
            $payload = null;
            if (is_null($scheduledMessage->getLastRequest()) || $scheduledMessage->getLastRequest() == '' || $scheduledMessage->getLastRequest() == 'null') {
                try {
                    $payload = $this->buildRequestForRemision($scheduledMessage);
                } catch (Throwable $exception) {
                    $numberOfRetries = $scheduledMessage->getNumberOfRetries();
                    $scheduledMessage->setNumberOfRetries(++$numberOfRetries);
                    $status = ScheduledMessageStatus::PENDING;
                    if ($numberOfRetries >= 2) {
                        $status = ScheduledMessageStatus::ERROR;
                    }
                    $scheduledMessage->setStatus($status);
                    $scheduledMessage->setLastRetry($this->date->date()->getTimestamp());
                    $scheduledMessage->setLastRequest(json_encode($payload));
                    $scheduledMessage->setLastResponse($exception->getMessage());
                    $this->monitorRepository->save($scheduledMessage);

                    if ($numberOfRetries >= 2) {
                        $this->mailHelper->generalFailureEmail($scheduledMessage, $exception->getMessage());
                    }
                    $this->logger->error("Error while creating N1 for order id " . json_encode($scheduledMessage->getOrderId())
                        . " Exception " . json_encode($exception->getMessage()));
                }
            } else {
                $payload = json_decode($scheduledMessage->getLastRequest());
            }

            if ($payload && $payload != "CLOSED") {
                $response = $this->service->executeCreaRemision($payload);
                $numberOfRetries = $scheduledMessage->getNumberOfRetries();
                $status = ScheduledMessageStatus::PROCESED;

                $scheduledMessage->setLastRetry($this->date->date()->getTimestamp());
                $scheduledMessage->setLastRequest(json_encode($payload));
                $responseFromWs = isset($response->error) ? $response->error : $response->getBody();

                if ((isset($response->error)) || ((int)$response->getStatusCode() != 200)) {
                    $scheduledMessage->setNumberOfRetries(++$numberOfRetries);
                    $status = ScheduledMessageStatus::PENDING;
                    if ($numberOfRetries >= 2) {
                        $status = ScheduledMessageStatus::ERROR;
                    }
                }

                $scheduledMessage->setLastResponse($responseFromWs);
                $scheduledMessage->setStatus($status);

                $this->monitorRepository->save($scheduledMessage);

                if ($numberOfRetries >= 2) {
                    $this->mailHelper->badComunicationErrorEmail($scheduledMessage, $payload, $responseFromWs);
                } else {
                    $this->scheduledMessagesHelper->confirmByMonitor($scheduledMessage->getOrderId(), "Pedido {$payload['order_number']} recibido por monitor.", true);
                }
            }
            if ($payload == 'CLOSED') {
                $status = ScheduledMessageStatus::PROCESED;
                $scheduledMessage->setStatus($status);
                $scheduledMessage->setLastRetry($this->date->date()->getTimestamp());
                $this->monitorRepository->save($scheduledMessage);
            }
        }
    }

    /**
     * @param $order
     * @return array
     */
    private function buildRequestForRemision($order)
    {
        $payload = [];
        $orderId = $order->getOrderId();
        $orderData = $this->scheduledMessagesHelper->getInfoFromOrder($orderId);
        if (strtoupper($orderData->getStatus()) == "CLOSED") {
            return "CLOSED";
        }

        if ($orderData) {
            $shippingAddress = $orderData->getShippingAddress();
            //$orderItems = $orderData->getItems();
            $paymentInfo = $orderData->getPayment();
            $shippingMethod = $orderData->getShippingMethod();
            $shippingMethod = $this->getShippingMethod($shippingMethod);
            $isPickupShipping = $shippingMethod == "pickup";
            $phoneNumber = preg_replace("/\s+/", "", $shippingAddress->getTelephone());

            $products = $this->buildProductosNode($orderData);
            $mediosPago = $this->buildMediosPagoNode($paymentInfo);
            $street = $shippingAddress->getStreet();

            $createdDate = date("Y-m-d H:i:s", $this->date->date()->getTimestamp() - 60);

            $payload = [
                "order_number" => $order->getOrderIncrementalId(),
                "city_name" => $shippingAddress->getCity(),
                "colina_name" => $street[0],//$shippingAddress->get getComunaCheckout(),
                "created_at" => $createdDate, //$orderData->getCreatedAt(),
                "email" => $orderData->getCustomerEmail(),
                "external_number" => isset($street[2]) ? $street[2] : "",
                "first_name" => $shippingAddress->getFirstname(),
                "last_name" => $shippingAddress->getLastname(),
                "municipio_name" => $shippingAddress->getCity(),
                "phone_number" => $phoneNumber,
                "state_name" => $shippingAddress->getRegion(),
                "street_name" => isset($street[1]) ? $street[1] : "",
                "internal_number" => "",
                "rut" => "",
                "tipo_envio" => $shippingMethod,
                "click_collect" => (bool) $isPickupShipping,
                "event_type" => "",
                "zip_code" => $shippingAddress->getPostcode(),
                "initial_hour" => "",//$startAndEndTime['startTime'],
                "final_hour" => "",
                "codigo_comercio" => $this->clientCode,
                "lat_direccion" => "",
                "long_direccion" => "",
                "home_type" => "CASA",
                "transaction_code" => "",
                "total_remision" => $orderData->getGrandTotal(),
                "currency_isocode" => $orderData->getOrderCurrencyCode(),
                "costo_despacho" => $orderData->getShippingAmount(),
                "requiere_validacion_qf" => false,
                "items" => $products,
                "posmessages" => [],
                "medios_pago" => [],
                "imgrecetas" => []
            ];

            if (sizeof($mediosPago) > 0) {
                $payload["medios_pago"] = $mediosPago;
            }
        }
        return $payload;
    }

    /**
     * @param $orderItems
     * @param $wsResponse
     * @param $storeToPickup
     * @return array
     */
    private function buildProductosNode($orderData)
    {
        $orderItems = $orderData->getItems();

        $remisionItems = [];
        foreach ($orderItems as $product) {
            if ($product->getBaseRowTotal() != 0) {
                $imageUrl = $this->helperImport->init($product, 'product_page_image_small')
                    ->setImageFile($product->getSmallImage()) // image,small_image,thumbnail
                    ->resize(380)
                    ->getUrl();

                if ($product->getQtyOrdered() > $product->getQtyRefunded()) {
                    //$specialPrice = $product->getOriginalPrice() - $product->getBasePrice();
                    $discountsNode = $this->buildDescuentosNode($product);
                    $remisionItem = [
                        "EAN1" => $product->getSku(),
                        "EAN2" => "",
                        "EAN3" => "",
                        "EAN4" => "",
                        "EAN5" => "",
                        "image_url" => $imageUrl,
                        "order_quantity" => (int)$product->getQtyOrdered() - (int)$product->getQtyRefunded(),
                        "material_sku" => $product->getSku(),
                        "material_name" => $product->getName(),
                        "fk_plant" => "000",
                        "fk_department" => '',
                        "fk_status" => 0,
                        "stock_availability" => 0,
                        "precio_unitario" => $product->getOriginalPrice(),
                        "descuento_total" => $discountsNode['totalDiscount'],
                        "iva" => 0.0,
                        "descuentos_aplicados" => $discountsNode['discounts']
                    ];
                    $remisionItems[] = $remisionItem;
                }
            }
        }
        return $remisionItems;
    }

    /**
     * @param $paymentData
     * @return array
     */
    private function buildMediosPagoNode($paymentData)
    {

        /***
         *  •	Forma Pago
        o	1 - WebPay
        o	2 - OneClick
        o	3 - Cybersource
        o	4 - Cash on delivery
        o	5 - PayPal
        o	6 - MercadoPago
        •	Tipo Pago
        o	1 - Débito
        o	2 - Crédito
        o	3 - No identificado
        o	4 - Efectivo

         ***/
        $medioPago = [];
        $tipoPago = $this->getPaymentType($paymentData->getMethod());
        if ($tipoPago === null){
            //$tipoPago = "mercado pago"; 
            $tipoPago = $paymentData['method']; 
            //$this->logger->info("segundo tipo pago. " . $tipoPago); 
        }
        switch ($tipoPago) {
            case "webpay":
                $paymentAdditionalInfo = ($paymentData->getAdditionalInformation());
                $rawDetails = $paymentAdditionalInfo['raw_details_info'];
                $formaPago = $this->getFormaPago($rawDetails['payment_type_code']);
                $medioPago[] = [
                    "forma_pago" => $formaPago,
                    "codigo_autorizacion" => $rawDetails['authorization_code'],
                    "monto" => $rawDetails['amount'],
                    "tipo_pago" => "1",
                    "codigo_tbk" => $rawDetails['commerce_code'],
                    "objeto_trx" => ""
                ];
                break;
            case "oneclick":
                $paymentAdditionalInfo = ($paymentData->getAdditionalInformation());
                $rawDetails = $paymentAdditionalInfo['raw_details_info'];
                $formaPago = $this->getFormaPago($rawDetails['payment_type_code']);
                $medioPago[] = [
                    "forma_pago" => $formaPago,
                    "codigo_autorizacion" => $rawDetails['authorization_code'],
                    "monto" => (double) $rawDetails['amount'],
                    "tipo_pago" => "2",
                    "codigo_tbk" => "",
                    "objeto_trx" => ""
                ];
                break;
            case "cybersource":
                $paymentAdditionalInfo = ($paymentData->getAdditionalInformation());
                $rawDetails = $paymentAdditionalInfo['raw_details_info'];
                //$formaPago = "cybersource";
                $medioPago[] = [
                    "forma_pago" => "3",
                    "codigo_autorizacion" => $rawDetails['authorization_code'],
                    "monto" => (double) $rawDetails['amount'],
                    "tipo_pago" => "3",
                    "codigo_tbk" => "",
                    "objeto_trx" => ""
                ];
                break;
            case "cashondelivery":
                $medioPago[] = [
                    "forma_pago" => "4",
                    "codigo_autorizacion" => "",
                    "monto" => 0,
                    "tipo_pago" => "3",
                    "codigo_tbk" => "",
                    "objeto_trx" => ""
                ];
                break;
            case "paypal":
                $medioPago[] = [
                    "forma_pago" => "5",
                    "codigo_autorizacion" => $paymentData->getLastTransId(),
                    "monto" => (double) $paymentData->getAmountAuthorized(),
                    "tipo_pago" => "3",
                    "codigo_tbk" => "",
                    "objeto_trx" => ""
                ];
                break;
            case "mercadopago_custom":
                $medioPago[] = [
                    "forma_pago" => "6",
                    "codigo_autorizacion" => $paymentData['additional_information']['token'],// $paymentData->getLastTransId(),
                    "monto" => (double) $paymentData['additional_information']['total_amount'],                 
                    "tipo_pago" => "3",
                    "codigo_tbk" => "",
                    "objeto_trx" => ""
                ];
                break;
                case "kueski":
                    $medioPago[] = [
                        "forma_pago" => "7",
                        "codigo_autorizacion" => $paymentData['additional_information']['kueski ID'],// $paymentData->getLastTransId(),
                        "monto" => (double) $paymentData['base_amount_ordered'],
                        "tipo_pago" => "3",
                        "codigo_tbk" => "",
                        "objeto_trx" => ""
                    ];
                    break;
                case "aplazo_payment":
                    $medioPago[] = [
                        "forma_pago" => "8",
                        "codigo_autorizacion" => $paymentData['last_trans_id'],// $paymentData->getLastTransId(),
                        "monto" => (double) $paymentData['base_amount_ordered'],
                        "tipo_pago" => "3",
                        "codigo_tbk" => "",
                        "objeto_trx" => ""
                    ];
                    break;
        }
        return $medioPago;
    }

    /**
     * @param $shippingMethod
     * @return string
     */
    private function getShippingMethod($shippingMethod)
    {
        switch ($shippingMethod) {
            case "pickupshipping_pickupshipping":
                return "pickup";
            case "flatrate_flatrate":
                return "estandar";
            case "freeshipping_freeshipping":
                return "gratis";
            default:
                return "shipping";
        }
    }

    /**
     * @param $paymentMethod
     * @return string
     */
    private function getPaymentType($paymentMethod)
    {
        /**
         * According to ahumada requirements Magento estará enviando dos tipos de pago
         * (tipo_pago) “webpay” (1) y “oneclick” (2).
         *
         * Magento will send the value as a string, and monitor will change it into a numeric value
         * according to a dictionary on their side
         **/
        switch ($paymentMethod) {
            case "softtek_oneclick":
                return "oneclick";
            case "softtek_webpay":
            case "transbank_webpay":
                return "webpay";
            case "softtek_payment":
                return "cybersource";
            case "cashondelivery":
                return "cashondelivery";
            case "paypal_express":
                return "paypal";
        }
    }

    private function buildDescuentosNode($orderItem)
    {
        $discounts = [];
        $totalDiscounts = 0;
        if ($orderItem->getOriginalPrice() > $orderItem->getBasePrice()) {
            $totalDiscount = $orderItem->getOriginalPrice() - $orderItem->getBasePrice();
            $discounts[] = [
                "tipo" => "PRECIO REBAJADO",
                "valor_descuento" => (double) $totalDiscount,
                "codigo_descuento" => "",
                "descripcion_descuento" =>"PRECIO REBAJADO",
                "aplicar" => true
            ];
            $totalDiscounts += $totalDiscount;
        }
        $ruleIds = is_null($orderItem->getAppliedRuleIds()) ? [] : explode(',', $orderItem->getAppliedRuleIds());
        foreach ($ruleIds as $ruleId) {
            $shoppingCartRuleData = $this->rule->getById($ruleId);
            if (!$shoppingCartRuleData->getApplyToShipping()) {
                $totalDiscount = (double) $orderItem->getDiscountAmount();
                $discounts[] = [
                    "tipo" => $shoppingCartRuleData->getCouponType(),
                    "valor_descuento" => (double) $orderItem->getDiscountAmount(),
                    "codigo_descuento" => $shoppingCartRuleData->getName(),
                    "descripcion_descuento" => $shoppingCartRuleData->getDescription(),
                    "aplicar" => true
                ];
                $totalDiscounts += $totalDiscount;
            }
        }
        return ["discounts" => $discounts, "totalDiscount" => $totalDiscounts];
    }
}
