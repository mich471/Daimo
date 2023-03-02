<?php


namespace Softtek\MonitorIntegration\Observer;


use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Catalog\Model\ProductFactory;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Softtek\MonitorIntegration\Service\MonitorIntegrationService;


class BeforeCheckoutObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var UrlInterface
     */
    private $_url;

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var ProductFactory
     */
    private $_productloader;

    /**
     * @var MessageManagerInterface
     */
    private $messageManager;
    /**
     * @var SourceRepositoryInterface
     */
    private $sourceRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var MonitorIntegrationService
     */
    private $monitorIntegrationService;
    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var string
     */
    protected $cartIndexUrl = 'checkout/cart/index';

    /**
     * @var ResponseFactory
     */
    protected $_responseFactory;

    /**
     * BeforeCheckoutObserver constructor.
     * @param ResponseFactory $responseFactory
     * @param UrlInterface $url
     * @param Cart $cart
     * @param ProductFactory $_productloader
     * @param MessageManagerInterface $messageManager
     * @param SourceRepositoryInterface $sourceRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilderFactory
     * @param MonitorIntegrationService $monitorIntegrationService
     * @param Session $customerSession
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        ResponseFactory $responseFactory,
        UrlInterface $url,
        Cart $cart,
        ProductFactory $_productloader,
        MessageManagerInterface $messageManager,
        SourceRepositoryInterface $sourceRepository,
        SearchCriteriaBuilder $searchCriteriaBuilderFactory,
        MonitorIntegrationService $monitorIntegrationService,
        Session $customerSession,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
        $this->cart = $cart;
        $this->_productloader = $_productloader;
        $this->messageManager = $messageManager;
        $this->sourceRepository = $sourceRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilderFactory;
        $this->monitorIntegrationService = $monitorIntegrationService;
        $this->customerSession = $customerSession;
        $this->logger = $logger;
    }


    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        return;
        //As a precaution, clear the stockInfo evertime the user tries to validate the cart
        $this->customerSession->unsStockInfo();

        $cartItems = $this->cart->getQuote()->getAllItems();
        $sources = $this->getSourcesList();
        $currentStock = $this->getStockFromAhumada($sources, $cartItems);
        if (!$currentStock) {
            $this->logger->error("Error from WebService ");
            $this->showErrorAndExit("En estos momentos nos encontramos con problemas en el servicio, por favor reintente mas tarde.");
        }
        $products = $currentStock["products"];
        $stock = $currentStock["stock"];
        $storesWithFullStock = $this->validateStock($stock, $products);
        if (empty($storesWithFullStock)) {
            $this->validatePartialStock($stock, $products);
            $this->showErrorAndExit("");
        }
    }

    /**
     * Get All source list
     *
     * @return SourceInterface[]|null
     */
    private function getSourcesList()
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter("enabled",1)
            ->create();
        try {
            $sourceData = $this->sourceRepository->getList($searchCriteria);
            if ($sourceData->getTotalCount()) {
                return $sourceData->getItems();
            }
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
        return null;
    }

    /**
     * @param $stores
     * @param $cartItems
     * @return array
     */
    private function getStockFromAhumada($stores, $cartItems)
    {
        $products = $this->getProductInfo($cartItems);
        $sources = [];
        foreach ($stores as $storesNearCoordinate) {
            $sourceCode = $storesNearCoordinate->getSourceCode();
            if ($sourceCode != "default") {
                if($sourceCode == "1"){
                    $storeRequest = [
                        "numero_local" => $storesNearCoordinate->getSourceCode(),
                        "stock" => $products
                    ];
                    array_push($sources, $storeRequest);
                } 
            }
        }
        $sourcesRequest = [ 
            "codigo_comercio" => "Sportico",
            "local" => $sources];
        $stock = $this->monitorIntegrationService->getStock($sourcesRequest);

        if (!$stock || !isset($stock->local)) {
            return null;
        }

        $response = [
            "stock" => $stock,
            "products" => $products
        ];
        return $response;
    }

    /**
     * @param $cartItems
     * @return array
     */
    private function getProductInfo($cartItems)
    {
        $products = [];
        foreach ($cartItems as $cartItem) {
            if (is_null($cartItem->getParentItemId())) {
                $productInfo = [
                    "codigo_producto" => $cartItem->getSku(),
                    "cantidad" => (int)$cartItem->getQty()
                ];
                array_push($products, $productInfo);
            }
        }
        return $products;
    }

    /**
     * @param $message
     */
    private function showErrorAndExit($message)
    {
        if ($message != "") {
            $this->messageManager->addError($message);
        }

        $cartUrl = $this->_url->getUrl($this->cartIndexUrl);
        $this->_responseFactory->create()->setRedirect($cartUrl)->sendResponse();
        exit;
    }

    /**
     * @param $liveTimeStock
     * @param $cartItems
     * @return array
     */
    private function validateStock($liveTimeStock, $cartItems)
    {
        $storesWithStock = [];
        $i = 0;
        $storesWithFullStock = [];
        $this->logger->info("Live Stock " . json_encode($liveTimeStock));
        if (isset($liveTimeStock->local)) {
            foreach ($liveTimeStock->local as $products) {
                $storeNumber = $products->numero_local;
                $stock = $products->stock;
                $productStatus = [$storeNumber => []];
                foreach ($stock as $key1 => $product) {
                    foreach ($cartItems as $key2 => $cartItem) {
                        if ($product->codigo_producto == $cartItem['codigo_producto'] && $product->cantidad >= $cartItem['cantidad']) {
                            array_push($productStatus[$storeNumber], $cartItem['codigo_producto']);
                        }
                    }
                }
                $storesWithStock[] = $productStatus;
            }
        }

        while ($i < sizeof($storesWithStock)) {
            $currentStore = array_keys($storesWithStock[$i]);
            $stores = array_values($storesWithStock[$i]);

            if (sizeof($stores[0]) >= sizeof($cartItems)) {
                $storesWithFullStock[] = $currentStore[0];
            }
            $i++;
        }
        return $storesWithFullStock;
    }

    /**
     * @param $currentStock
     * @param $cartItems
     */
    private function validatePartialStock($currentStock, $cartItems)
    {
        $storesWithStock = [];
        $this->logger->info("STock " . json_encode($currentStock));

        foreach ($currentStock->local as $products) {
            $storeNumber = $products->numero_local;
            $stock = $products->stock;
            $productStatus = ["storeNumber" => $storeNumber, "stock" => []];
            foreach ($stock as $key1 => $product) {
                foreach ($cartItems as $key2 => $cartItem) {
                    if ($product->codigo_producto == $cartItem['codigo_producto']) {
                        $status = $this->getStatusForProduct($product->cantidad, $cartItem['cantidad']);
                        $stockByProduct = [
                            "product" => $cartItem['codigo_producto'],
                            "availableStock" => $product->cantidad,
                            "status" => $status];
                        array_push($productStatus["stock"], $stockByProduct);
                    }
                }
            }
            $storesWithStock[] = $productStatus;
        }

        usort($storesWithStock, function ($a, $b) {
            if (sizeof($a["stock"]) == sizeof($b["stock"])) {
                return 0;
            }
            return ($a["stock"] < $b["stock"]) ? 1 : -1;
        });
        $storesWithGreaterStock = $storesWithStock[0];
        $this->setStockInfoInSession($storesWithGreaterStock['stock']);
    }

    /**
     * @param $stock
     * @param $cartQty
     * @return int
     */
    private function getStatusForProduct($stock, $cartQty)
    {
        if ($stock >= $cartQty) {
            return 1;
        } elseif ($stock == 0) {
            return 0;
        } elseif ($stock < $cartQty) {
            return -1;
        }
    }

    /**
     * @param $stockInfo
     */
    private function setStockInfoInSession($stockInfo)
    {
        $this->customerSession->setStockInfo($stockInfo);
    }

    /**
     * @param $productId
     * @return \Magento\Catalog\Model\Product
     */
    private function getProductById($productId)
    {
        return $this->_productloader->create()->load($productId);
    }
}
