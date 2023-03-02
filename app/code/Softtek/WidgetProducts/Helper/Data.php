<?php

namespace Softtek\WidgetProducts\Helper;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Block\Product\ReviewRendererInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestSellersCollectionFactory;
use Magento\Reports\Model\ResourceModel\Product\CollectionFactory as MostViewedCollectionFactory;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ProductFactory;

class Data extends AbstractHelper
{
    protected $_productCollectionFactory;

    protected $_bestSellersCollectionFactory;

    protected $_mostViewedCollectionFactor;

    protected $_listProduct;

    protected $_reviewRenderer;

    protected $_layout;

    protected $_storeManager;

    protected $_productFactory;

    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        BestSellersCollectionFactory $bestSellersCollectionFactory,
        MostViewedCollectionFactory $mostViewedCollectionFactor,
        ListProduct $listProduct,
        ReviewRendererInterface $reviewRenderer,
        LayoutInterface $layout,
        StoreManagerInterface $storeManager,
        ProductFactory $productFactory
    )
    {
        $this->_bestSellersCollectionFactory = $bestSellersCollectionFactory;
        $this->_mostViewedCollectionFactor = $mostViewedCollectionFactor;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_listProduct = $listProduct;
        $this->_reviewRenderer = $reviewRenderer;
        $this->_layout = $layout;
        $this->_storeManager = $storeManager;
        $this->_productFactory = $productFactory;
        parent::__construct($context);
    }

    public function getMostViewedCollection($productsCount)
    {
        $currentStoreId = $this->_storeManager->getStore()->getId();

        $collection = $this->_mostViewedCollectionFactor->create()
            ->addAttributeToSelect('*')
            ->addViewsCount()
            ->setStoreId($currentStoreId)
            ->addStoreFilter($currentStoreId)
            ->setPageSize($productsCount);
        return $collection->getItems();
    }

    public function getBestSellersCollection($productsCount)
    {
        $currentStoreId = $this->_storeManager->getStore()->getId();

        return $this->_bestSellersCollectionFactory->create()
            ->setModel(Product::class)
            ->addStoreFilter($currentStoreId)
            ->setPageSize($productsCount);
    }

    public function getListProduct()
    {
        return $this->_listProduct;
    }

    public function getLazyLoadedImage($productImage){
        return $productImage->toHtml();
    }

    public function getReviewsSummaryHtml(
        Product $product,
        $templateType = false,
        $displayIfNoReviews = false
    ) {
        return $this->_reviewRenderer->getReviewsSummaryHtml($product, $templateType, $displayIfNoReviews);
    }

    public function getProductPriceHtml(
        Product $product,
        $priceType = null,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['price_id'] = $arguments['price_id'] ?? 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = $arguments['include_container'] ?? true;
        $arguments['display_minimal_price'] = $arguments['display_minimal_price'] ?? true;

        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->_layout->getBlock('product.price.render.default');
        if (!$priceRender) {
            $priceRender = $this->_layout->createBlock(
                \Magento\Framework\Pricing\Render::class,
                'product.price.render.default',
                ['data' => ['price_render_handle' => 'catalog_product_prices']]
            );
        }

        return $priceRender->render(
            FinalPrice::PRICE_CODE,
            $product,
            $arguments
        );
    }

    public function getLoadProduct($id)
    {
        return $this->_productFactory->create()->load($id);
    }
}
