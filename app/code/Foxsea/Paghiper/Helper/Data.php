<?php
namespace Foxsea\Paghiper\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Checkout\Model\Session;
use Purpletree\Marketplace\Model\ResourceModel\Seller;
use Magento\Catalog\Model\ProductRepository;
use Foxsea\Paghiper\Model\Api\TransactionSearch\Client;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;

class Data extends AbstractHelper
{
    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var integer
     */
    protected $sellerId;

    /**
     * @var integer
     */
    protected $sellerStore;

    /**
     * @var Seller
     */
    protected $storeDetails;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var Client
     */
    protected $apiClient;

    protected $scopeConfig;
    protected $storeManager;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager;
     * @param ScopeConfigInterface $scopeConfig;
     * @param EncryptorInterface $encryptor
     * @param Session $checkoutSession
     * @param Seller $storeDetails
     * @param ProductRepository $productRepository
     * @param Client $apiClient
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        Session $checkoutSession,
        Seller $storeDetails,
        ProductRepository $productRepository,
        Client $apiClient
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->encryptor = $encryptor;
        $this->checkoutSession = $checkoutSession;
        $this->storeDetails = $storeDetails;
        $this->productRepository = $productRepository;
        $this->apiClient = $apiClient;
    }

    public function getApiUrl() {
        return 'https://api.softtek.com/';
    }

    public function getApiKey() {
        return $this->scopeConfig->getValue('payment/foxsea_paghiper/apikey', ScopeInterface::SCOPE_WEBSITE);
    }

    public function getToken() {
        return $this->scopeConfig->getValue('payment/foxsea_paghiper/token', ScopeInterface::SCOPE_WEBSITE);
    }

    public function getConfig($config) {
        return $this->scopeConfig->getValue('payment/foxsea_paghiper/' . $config, ScopeInterface::SCOPE_WEBSITE);
    }

    public function getNotificationUrl() {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB) . 'paghiper/order/update';
    }

    /**
     * Get PT Transaction Key
     *
     * @return string
     */
    public function getPtTransactionKey()
    {
        if ($storeDetails = $this->getSellerStore()) {
            return $this->encryptor->decrypt($storeDetails['cs_pt_rest_api_key']);
        }

        return '';
    }

    /**
     * Get PT Merchant ID
     *
     * @return string
     */
    public function getPtMerchantId()
    {
        if ($storeDetails = $this->getSellerStore()) {
            return $storeDetails['cs_pt_merchant_id'];
        }

        return '';
    }

    /**
     * Get Seller ID
     *
     * @return integer
     */
    protected function getSellerId()
    {
        if (is_null($this->sellerId)) {
            foreach ($this->checkoutSession->getQuote()->getAllItems() as $item) {
                $product = $this->productRepository->getById($item->getProductId());
                $this->sellerId = $product->getSellerId();
                break;
            }
        }

        return $this->sellerId;
    }

    /**
     * @return integer
     */
    protected function getSellerStore()
    {
        if (is_null($this->sellerStore)) {
            $this->sellerStore = $this->storeDetails->getStoreDetails($this->getSellerId());
        }

        return $this->sellerStore;
    }

    /**
     * Get Boleto Payment Event Status from CyberSource
     *
     * @param Order $order
     * @param string $requestId
     * @throws LocalizedException
     */
    public function getBoletoPaymentEventStatus($order, $requestId)
    {
        if (
            $this->getConfig('cs_tsa_simulate_approved_boletos')
            && $this->getConfig('cs_tsa_host') == 'apitest.cybersource.com'
        ) {
            return 'Fulfilled';
        }
        $response = $this->apiClient->apiCall('tss/v2/transactions/' . $requestId, [], $method = 'GET', $order);
        if ($response['success']) {
            if (isset($response['data']) && isset($response['data']['processorInformation']['eventStatus'])) {
                return $response['data']['processorInformation']['eventStatus'];
            }
        } else {
            throw new LocalizedException(__('Error getting up CyberSource payment info for order #%1, request ID %2: %3', $order->getIncrementId(), $requestId, $response['error']));
        }

        return '';
    }
}
