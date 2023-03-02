<?php

namespace WeltPixel\GoogleTagManager\Block;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Url\EncoderInterface;
use Magento\Widget\Block\BlockInterface;
use Mageplaza\Productslider\Helper\Data;

/**
 * Class AbstractSlider
 * @package Mageplaza\Productslider\Block
 */
abstract class AbstractSlider extends AbstractProduct implements BlockInterface, IdentityInterface
{

    /**
     * AbstractSlider constructor.
     *
     * @param Context $context
     * @param Data $helperData
     * @param HttpContext $httpContext
     * @param EncoderInterface $urlEncoder
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @param Product $product
     * @param null $priceType
     * @param string $renderZone
     * @param array $arguments
     *
     * @return string
     * @throws LocalizedException
     */
    public function getProductPriceHtml(
        Product $product,
        $priceType = null,
        $renderZone = Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['price_id'] = isset($arguments['price_id'])
            ? $arguments['price_id']
            : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container'])
            ? $arguments['include_container']
            : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
            ? $arguments['display_minimal_price']
            : true;

        /** @var Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        if (!$priceRender) {
            $priceRender = $this->getLayout()->createBlock(
                Render::class,
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
}
