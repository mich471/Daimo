<?php

namespace MW\ImproveMarketplace\Plugin;

class CustomerDataItem
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * CustomerDataItem constructor.
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * @param \Magento\Checkout\CustomerData\AbstractItem $subject
     * @param $data
     * @return mixed
     */
    public function afterGetItemData(
        \Magento\Checkout\CustomerData\AbstractItem $subject,
        $data
    ) {
        if (!isset($data['seller_id'])) {
            $productSku = $data['product_sku'] ?? null;
            if ($productSku) {
                try {
                    $product = $this->productRepository->get($productSku);
                    $data['seller_id'] = $product->getSellerId();
                } catch (\Exception $e) {

                }
            }
        }

        return $data;
    }
}