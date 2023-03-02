<?php
namespace Foxsea\Paghiper\Model\Api\TransactionSearch;

use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Purpletree\Marketplace\Model\ResourceModel\Seller;
use Magento\Catalog\Model\ProductRepository;

/**
 * Client class for CyberSource Transaction Search API interface.
 */
class Client
{
    /**
     * @var ZendClientFactory
     */
    protected $_clientFactory;

    /**
     * @var Json
     */
    protected $_json;

    /**
     * @var Context
     */
    protected $_context;

    /**
     * @var ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var string
     */
    protected $_endPointBase;

    /**
     * @var array
     */
    protected $_headers;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var object
     */
    protected $_relatedObject;

    /**
     * @var EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @var Seller
     */
    protected $_storeDetails;

    /**
     * @var ProductRepository
     */
    protected $_productRepository;

    /**
     * @var integer
     */
    protected $_sellerId;

    /**
     * @var integer
     */
    protected $_sellerStore;

    /**
     * Test constructor.
     *
     * @param ZendClientFactory $clientFactory
     * @param Json $json
     * @param Context $context
     * @param array $data
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     * @param Seller $storeDetails
     * @param ProductRepository $productRepository
     */
    public function __construct(
        ZendClientFactory $clientFactory,
        Json $json,
        Context $context,
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        Seller $storeDetails,
        ProductRepository $productRepository,
        array $data = []
    ) {
        $this->_clientFactory = $clientFactory;
        $this->_json = $json;
        $this->_context        = $context;
        $this->_messageManager = $context->getMessageManager();
        $this->_scopeConfig = $scopeConfig;
        $this->_encryptor = $encryptor;
        $this->_storeDetails = $storeDetails;
        $this->_productRepository = $productRepository;
    }

    /**
     * Set Endpoint Base.
     *
     * @param string $endPointBase
     * @return void
     */
    public function setEndPointBase($endPointBase)
    {
        $this->_endPointBase = $endPointBase;
    }

    /**
     * Get Endpoint Base.
     *
     * @return string
     */
    public function getEndPointBase()
    {
        return $this->_endPointBase;
    }

    /**
     * Set Related Object.
     *
     * @param object $relatedObject
     * @return void
     */
    public function setRelatedObject($relatedObject)
    {
        $this->_relatedObject = $relatedObject;
    }

    /**
     * Get Related Object.
     *
     * @return string
     */
    public function getRelatedObject()
    {
        return $this->_relatedObject;
    }

    /**
     * Set request headers.
     *
     * @param array $headers
     *
     * return void
     */
    public function setHeaders($headers)
    {
        $this->_headers = $headers;
    }

    /**
     * Get request headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * Get HTTP client.
     *
     * @return ZendClientFactory
     */
    public function getHttpClient()
    {
        return $this->_clientFactory;
    }

    /**
     * Get Config
     * @param string $path
     * @return string
     */
    public function getConfig($path) {
        return $this->_scopeConfig->getValue('payment/foxsea_paghiper/' . $path, ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * Get Seller ID
     *
     * @param Order|boolean $order
     * @return integer
     */
    protected function getSellerId()
    {
        if (is_null($this->_sellerId)) {
            if ($this->getRelatedObject() && $this->getRelatedObject() instanceof Order) {
                foreach ($this->getRelatedObject()->getAllItems() as $item) {
                    $product = $this->_productRepository->getById($item->getProductId());
                    $this->_sellerId = $product->getSellerId();
                    break;
                }
            }
        }

        return $this->_sellerId;
    }

    /**
     * @param Order|boolean
     * @return integer
     */
    protected function getSellerStore($order = false)
    {
        if (is_null($this->_sellerStore)) {
            $this->_sellerStore = $this->_storeDetails->getStoreDetails($this->getSellerId($order));
        }

        return $this->_sellerStore;
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
     * Get TSA Key
     *
     * @return string
     */
    public function getTsaPtApiKey()
    {
        if ($storeDetails = $this->getSellerStore()) {
            return $storeDetails['cs_tsa_pt_rest_api_key'];
        }

        return '';
    }

    /**
     * Get TSA Secret Key
     *
     * @return string
     */
    public function getTsaPtApiSecret()
    {
        if ($storeDetails = $this->getSellerStore()) {
            return $this->_encryptor->decrypt($storeDetails['cs_tsa_pt_rest_api_secret_key']);
        }

        return '';
    }

    /**
     * Make call to the CyberSource Transaction Search API.
     *
     * @param string $action
     * @param array $payload
     * @param string $method
     * @param mixed $relatedObject
     * @return array $result
     */
    public function apiCall($action, $payload = [], $method = 'GET', $relatedObject = false)
    {
        try {
            $result = ['success' => false];
            $endPointBase = $this->getConfig('cs_tsa_host');
            $this->setEndPointBase("https://" . $endPointBase);
            $url = $this->getEndPointBase().'/'.$action;
            $client = $this->getHttpClient()->create();
            $client->setUri($url);
            $client->setMethod($method);
            $client->setConfig(['maxredirects' => 0, 'timeout' => 30]);

            $this->setRelatedObject($relatedObject);
            $signatureString = "host: " . $this->getConfig('cs_tsa_host') . "\n(request-target): " . "get" . " " . "/" . $action . "\nv-c-merchant-id: " . $this->getPtMerchantId();
            $decodeKey = base64_decode($this->getTsaPtApiSecret());
            $signatureByteString = utf8_encode($signatureString);
            $signature = base64_encode(hash_hmac("sha256", $signatureByteString, $decodeKey, true));
            $signatureHeader = 'keyid="' . $this->getTsaPtApiKey() . '", algorithm="HmacSHA256", headers="host (request-target) v-c-merchant-id", signature="' . $signature . '"';

            $headers = [
                'host' => $this->getConfig('cs_tsa_host'),
                'signature' => $signatureHeader,
                'v-c-merchant-id' => $this->getPtMerchantId(),
                'v-c-date' => gmdate('D, d M Y H:i:s \G\M\T', time())
            ];
            foreach ($headers as $hk => $hv) {
                $client->setHeaders($hk, $hv);
            }

            if (count($payload)) {
                $client->setHeaders('Content-Type', 'application/json');
                $client->setRawData($this->_json->serialize($payload));
            }

            $response = $client->request();
            $responseBody = $response->getBody();

            $responseBodyArray = $this->_json->unserialize($responseBody);
            if ($response->getStatus() == 200 || $response->getStatus() == 201) {
                if ($response->getStatus() != "500") {
                    $result['success'] = true;
                    if (is_array($responseBodyArray)) {
                        $result['data'] = $responseBodyArray;
                    }
                } elseif (isset($responseBodyArray['response']['rmsg'])) {
                    $result['error'] = __($responseBodyArray['response']['rmsg'])->getText();
                    $result['code'] = $response->getStatus();
                } else {
                    $result['success'] = true;
                    $result['data'] = $responseBodyArray;
                }
            } else {
                $result['code'] = "";
                $result['error'] = __($responseBodyArray['response']['rmsg'])->getText();
                if ($response->getStatus()) {
                    $result['code'] = $response->getStatus();
                }
            }
        } catch (\Exception $e) {
            $result['error'] = __($e->getMessage())->getText();
            $result['code'] = $e->getCode();
        }

        return $result;
    }
}
