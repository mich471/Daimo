<?php
namespace Foxsea\Paghiper\Cron;

use Magento\Framework\App\Area;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Model\Spi\OrderResourceInterface;
use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Foxsea\Paghiper\Helper\Data;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Purpletree\Marketplace\Model\ResourceModel\Sellerorder\CollectionFactory as SellerOrderCollectionFactory;
use Purpletree\Marketplace\Model\ResourceModel\Sellerorder;
use Purpletree\Marketplace\Model\SellerorderFactory;
use Magento\Sales\Api\OrderManagementInterface;
use Purpletree\Marketplace\Helper\Data as HelperData;
use Magento\Customer\Model\Customer;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Framework\App\State;
use Magento\Framework\App\AreaList;

class BoletoStatus
{
    /**
     * @var CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var OrderResourceInterface
     */
    protected $_orderResource;

    /**
     * @var OrderInterfaceFactory
     */
    protected $_orderFactory;

    /**
     * @var Json
     */
    protected $_json;

    /**
     * @var DateTime
     */
    protected $_dateTime;

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var UrlInterface
     */
    protected $_url;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var StateInterface
     */
    protected $_inlineTranslation;

    /**
     * @var TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var SellerOrderCollectionFactory
     */
    protected $_sellerOrderCollectionFactory;

    /**
     * @var Sellerorder
     */
    protected $_sellerorder;

    /**
     * @var SellerorderFactory
     */
    protected $_sellerorderFactory;

    /**
     * @var OrderManagementInterface
     */
    protected $_orderManagement;

    /**
     * @var HelperData
     */
    protected $_dataHelper;

    /**
     * @var Customer
     */
    protected $_seller;

    /**
     * @var ProductRepository
     */
    protected $_productRepository;

    /**
     * @var PriceHelper
     */
    protected $_priceHelper;

    /**
     * @var Renderer
     */
    protected $_addressRenderer;

    /**
     * @var PaymentHelper
     */
    protected $_paymentHelper;

    /**
     * @var State
     */
    protected $_state;

    /**
     * @var AreaList
     */
    protected $_areaList;

    /**
     * BoletoStatus constructor.
     *
     * @param CollectionFactory $orderCollectionFactory
     * @param OrderResourceInterface $orderResource
     * @param OrderInterfaceFactory $orderFactory
     * @param Json $json
     * @param DateTime $dateTime
     * @param Data $helper
     * @param StoreManagerInterface $storeManager
     * @param UrlInterface $url
     * @param ScopeConfigInterface $scopeConfig
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param SellerOrderCollectionFactory $sellerOrderCollectionFactory
     * @param Sellerorder $sellerorder
     * @param SellerorderFactory $sellerorderFactory
     * @param OrderManagementInterface $orderManagement
     * @param HelperData $dataHelper
     * @param Customer $seller
     * @param ProductRepository $productRepository
     * @param PriceHelper $priceHelper
     * @param Renderer $addressRenderer
     * @param PaymentHelper $paymentHelper
     * @param State $state
     * @param AreaList $areaList
     */
    public function __construct(
        CollectionFactory $orderCollectionFactory,
        OrderResourceInterface $orderResource,
        OrderInterfaceFactory $orderFactory,
        Json $json,
        DateTime $dateTime,
        Data $helper,
        StoreManagerInterface $storeManager,
        UrlInterface $url,
        ScopeConfigInterface $scopeConfig,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        SellerOrderCollectionFactory $sellerOrderCollectionFactory,
        Sellerorder $sellerorder,
        SellerorderFactory $sellerorderFactory,
        OrderManagementInterface $orderManagement,
        HelperData $dataHelper,
        Customer $seller,
        ProductRepository $productRepository,
        PriceHelper $priceHelper,
        Renderer $addressRenderer,
        PaymentHelper $paymentHelper,
        State $state,
        AreaList $areaList
    )
    {
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_orderResource = $orderResource;
        $this->_orderFactory = $orderFactory;
        $this->_json = $json;
        $this->_dateTime = $dateTime;
        $this->_helper = $helper;
        $this->_storeManager = $storeManager;
        $this->_url = $url;
        $this->_scopeConfig = $scopeConfig;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->_sellerOrderCollectionFactory = $sellerOrderCollectionFactory;
        $this->_sellerorder = $sellerorder;
        $this->_sellerorderFactory = $sellerorderFactory;
        $this->_orderManagement = $orderManagement;
        $this->_dataHelper = $dataHelper;
        $this->_seller = $seller;
        $this->_productRepository = $productRepository;
        $this->_priceHelper = $priceHelper;
        $this->_addressRenderer = $addressRenderer;
        $this->_paymentHelper = $paymentHelper;
        $this->_state = $state;
        $this->_areaList = $areaList;
    }

    /**
     * Get last orders paid with Boleto Bancario to consult status on CyberSource
     *
     * @return BoletoStatus|void
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->_orderCollectionFactory->create()
            ->addFieldToSelect(['entity_id', 'status'])
            ->addFieldToFilter('main_table.status', ['eq' => 'payment_review'])
            ->setOrder(
                'main_table.created_at',
                'asc'
            )->setPageSize(100);

        $collection->getSelect()
            ->join(
                ["payment" => $collection->getConnection()->getTableName('sales_order_payment')],
                'main_table.entity_id = payment.parent_id',
                array('additional_information')
            )
            ->where('payment.method = ?', 'foxsea_paghiper');

        $collection->getSelect()->where('main_table.st_cs_last_api_check < (NOW() - INTERVAL 2 HOUR) AND main_table.st_cs_last_api_check > (NOW() - INTERVAL 1 DAY)');

        if (!$this->_state->getAreaCode()) {
            $this->_state->setAreaCode(Area::AREA_FRONTEND);
        }
        $this->_areaList->getArea(Area::AREA_CRONTAB)
            ->load(Area::PART_TRANSLATE);

        foreach ($collection as $orderItem) {
            $order = $this->_orderFactory->create();
            $this->_orderResource->load($order, $orderItem->getId(), OrderInterface::ENTITY_ID);
            $order->setStCsLastApiCheck($this->_dateTime->gmtDate());
            $order->save();
            $additionalInfoJson = $orderItem->getAdditionalInformation();
            $additionalInfoArray = [];
            try {
                $additionalInfoArray = $this->_json->unserialize($additionalInfoJson);
            } catch (\Exception $e) {
                throw new LocalizedException(__('Error getting up the additional information json value from order ID %1', $orderItem->getId()));
            }

            if (!isset($additionalInfoArray['request_id'])) {
                throw new LocalizedException(__('Missing request_id for order ID %1', $orderItem->getId()));
            }

            $paymentEventStatus = $this->_helper->getBoletoPaymentEventStatus($order, $additionalInfoArray['request_id']);
            switch ($paymentEventStatus) {
                case "Expired";
                    if ($order->canCancel()) {
                        $order->cancel();
                        $order->save();
                        $this->updateSellerOrderStatus($order, Order::STATE_CANCELED);
                        $this->_addStatusHistory($order, Order::STATE_CANCELED, "Order canceled due to 'Expired' status on CyberSource");
                    } else {
                        throw new LocalizedException(__('Order ID %1 can not be cancelled', $orderItem->getId()));
                    }
                    break;
                case "Fulfilled";
                    $order->setState(Order::STATE_PROCESSING)->setStatus(Order::STATE_PROCESSING);
                    $order->save();
                    $this->updateSellerOrderStatus($order, Order::STATE_PROCESSING);
                    $this->_addStatusHistory($order, Order::STATE_PROCESSING, "Pagamento de boleto confirmado.", false);
                    $this->_sendOrderEmailToCustomer($order);
                    $this->_sendOrderEmailToSeller($order);
                    break;
            }
        }

        return $this;
    }

    /**
     * Add Order Status History record
     *
     * @return Order $order
     * @throws string $status
     * @throws string $message
     */
    protected function _addStatusHistory($order, $status, $message, $notify)
    {
        $history = $order->addStatusHistoryComment(__($message), $status);
        $history->setIsVisibleOnFront(1);
        $history->setIsCustomerNotified(0);
        if ($notify) {
            $this->sendNotificationToSellerAndCustomer($order, $message);
            $history->setIsCustomerNotified(1);
        }
        $history->setSmIsMessage(0);
        $history->setSmSellerMessage(0);
        $history->setSmCustomerMessage(0);
        $history->save();
    }

    /**
     * Send notification to seller and customer
     *
     * @return Order $order
     * @throws string $comment
     */
    protected function sendNotificationToSellerAndCustomer($order, $comment)
    {
        try {
            $templateOptions = array('area' => Area::AREA_FRONTEND, 'store' => $this->_storeManager->getStore()->getId());
            $templateVars = array(
                'store' => $this->_storeManager->getStore(),
                'store_name' => $this->_storeManager->getStore()->getName(),
                'receiver_name' => $order->getCustomerFirstname(),
                'order_number' => $order->getIncrementId(),
                'comment' => $comment,
                'order_url' => $this->_url->getUrl('sales/order/view', ['order_id' => $order->getId()])
            );

            $senderEmail = $this->_scopeConfig->getValue('trans_email/ident_general/email',ScopeInterface::SCOPE_STORE);
            $senderName = $this->_scopeConfig->getValue('trans_email/ident_general/name',ScopeInterface::SCOPE_STORE);
            $from = ['email' => $senderEmail, 'name' => $senderName];
            $this->_inlineTranslation->suspend();

            $sellerEmail = "";
            $orderCollection = $this->_sellerOrderCollectionFactory->create();
            $orderCollection->addFieldToSelect('entity_id')
                ->join(
                    ['seller' => $orderCollection->getConnection()->getTableName('customer_entity')],
                    'main_table.seller_id = seller.entity_id',
                    ['email']
                )
                ->addFieldToFilter('main_table.order_id', $order->getId());
            $orderCollection->load();
            if (count($orderCollection)) {
                $firstRecord = $orderCollection->getFirstItem();
                $sellerEmail = $firstRecord->getEmail();
            }

            $to = [$sellerEmail, $order->getCustomerEmail()];

            $templateId = $this->_scopeConfig->getValue ( 'payment/foxsea_paghiper/paid_boleto_template', ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
            $transport = $this->_transportBuilder->setTemplateIdentifier($templateId)->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($to)
                ->getTransport();
            $transport->sendMessage();
            $this->_inlineTranslation->resume();
        } catch (Exception $e) {
            throw new LocalizedException(__('Error sending paid boleto email notification'));
        }
    }

    /**
     * Update Seller Order Status
     *
     * @return Order $order
     */
    protected function updateSellerOrderStatus($order, $orderStatus)
    {
        $entity_ids  = $this->_sellerorder->getEntityIdfromOrderId2($order->getId());
        foreach ($entity_ids as $idd) {
            $sellerorder = $this->_sellerorderFactory->create();
            $sellerorder->load($idd['entity_id']);
            $sellerorder->setOrderStatus($orderStatus);
            $sellerorder->save();
        }
    }

    /**
     * Send order notification to customer
     *
     * @return Order $order
     */
    protected function _sendOrderEmailToCustomer($order)
    {
        $this->_orderManagement->notify($order->getEntityId());
    }

    /**
     * Send order notification to seller
     *
     * @return Order $order
     */
    protected function _sendOrderEmailToSeller($order)
    {
        $moduleEnable = $this->_dataHelper->getGeneralConfig('general/enabled');
        if ($moduleEnable) {
            $seller_orders = [];
            foreach ($order->getAllVisibleItems() as $items) {
                $product = $this->_productRepository->getById($items['product_id']);
                $seller_id = $product->getData('seller_id');
                if ($seller_id) {
                    $seller_orders[$seller_id][] = $items;
                }
            }
            //Email to Seller
            $identifier = 'vendor_order';
            foreach ($seller_orders as $seller_idd => $items) {
                $sellerObj = $this->_seller->load($seller_idd);
                $totalsss           = 0;
                $productshtml       = '';
                foreach ($items as $item) {
                    $optionsHtml       = '';
                    $getItemOptions    = $this->getItemOptions($item);
                    if (!empty($getItemOptions)) {
                        $optionsHtml .= '<dl class="item-options">';
                        foreach ($getItemOptions as $option) :
                            $optionsHtml .= '<dt><strong><em>'.$option["label"].'</em></strong></dt><dd>'.nl2br($option['value']).'</dd>';
                        endforeach;
                        $optionsHtml .= '</dl>';
                    }
                    $totalsss += $item->getRowTotalInclTax();
                    $productshtml .= '<tbody><tr><td class="item-info has-extra" style="font-family:\'Open Sans\',\'Helvetica Neue\',Helvetica,Arial,sans-serif;vertical-align:top;padding:10px;border-top:1px solid #ccc"><p class="product-name" style="margin-top:0;margin-bottom:5px;font-weight:700">'.$item->getName().'</p><p class="sku" style="margin-top:0;margin-bottom:10px">SKU: '.$item->getSku().'</p>'.$optionsHtml.'
                 </td><td class="item-qty" style="font-family:\'Open Sans\',\'Helvetica Neue\',Helvetica,Arial,sans-serif;vertical-align:top;padding:10px;border-top:1px solid #ccc;text-align:center">'.$item->getQtyOrdered().'</td><td class="item-price" style="font-family:\'Open Sans\',\'Helvetica Neue\',Helvetica,Arial,sans-serif;vertical-align:top;padding:10px;border-top:1px solid #ccc;text-align:right"><span class="price">'.$this->getCurrencyData($item->getRowTotalInclTax()).'</span></td></tr></tbody>';
                }
                $emailTemplateVariables                             = [];
                $emailTemplateVariables['order']                    = $order;
                $emailTemplateVariables['productshtml']             = $productshtml;
                $emailTemplateVariables['productstotalhtml']        = $this->getCurrencyData($totalsss);
                $emailTemplateVariables['payment_html']             = $this->getPaymentHtml($order);
                $emailTemplateVariables['formattedShippingAddress'] = $this->getFormattedShippingAddress($order);
                $emailTemplateVariables['formattedBillingAddress']  = $this->getFormattedBillingAddress($order);
                $emailTemplateVariables['seller_name']              = $sellerObj->getName();
                $sender = [
                    'name' => $this->getStoreName(),
                    'email' =>$this->getStoreEmail()
                ];
                $receiver = [
                    'name' =>$sellerObj->getName(),
                    'email' => $sellerObj->getEmail()
                ];
                $this->_dataHelper->yourCustomMailSendMethod(
                    $emailTemplateVariables,
                    $sender,
                    $receiver,
                    $identifier
                );
            }
            //Email to Seller
        }
    }

    /**
     * Get Store Email
     *
     * @return string
     */
    protected function getStoreEmail()
    {
        return $this->_scopeConfig
            ->getValue('trans_email/ident_sales/email');
    }

    /**
     * Get Store Name
     *
     * @return string
     */
    protected function getStoreName()
    {
        return $this->_scopeConfig
            ->getValue('trans_email/ident_sales/name');
    }

    /**
     * Get Formatted Billing Address
     *
     * @return string
     */
    protected function getFormattedBillingAddress($order)
    {
        return $this->_addressRenderer->format($order->getBillingAddress(), 'html');
    }

    /**
     * @param Order $order
     * @return string|null
     */
    protected function getFormattedShippingAddress($order)
    {
        return $order->getIsVirtual()
            ? null
            : $this->_addressRenderer->format($order->getShippingAddress(), 'html');
    }

    /**
     * Returns payment info block as HTML.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     *
     * @return string
     */
    protected function getPaymentHtml(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        return $this->_paymentHelper->getInfoBlockHtml(
            $order->getPayment(),
            $order->getStoreId()
        );
    }

    /**
     * Get Item Options
     *
     * @param Object $orderitem
     * @return Array
     */
    protected function getItemOptions($orderitem)
    {
        $result = [];
        if ($options = $orderitem->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }

        return $result;
    }

    /**
     * Get Currency Data
     *
     * @return String
     */
    protected function getCurrencyData($price)
    {
        return $this->_priceHelper->currency($price, true, false);
    }
}
