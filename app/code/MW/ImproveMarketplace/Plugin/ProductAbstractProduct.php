<?php

namespace MW\ImproveMarketplace\Plugin;

class ProductAbstractProduct
{
    /**
     * @param \Magento\Catalog\Block\Product\AbstractProduct $subject
     * @param $product
     * @param $additional
     * @return array
     */
    public function beforeGetAddToCartUrl(
        \Magento\Catalog\Block\Product\AbstractProduct $subject,
        $product,
        $additional
    ) {
        $additional['seller_id'] = $product->getSellerId();

        return [$product, $additional];
    }
}
