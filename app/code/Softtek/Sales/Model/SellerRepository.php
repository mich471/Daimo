<?php

namespace Softtek\Sales\Model;

use Softtek\Sales\Model\ResourceModel\Sellerorder;

class SellerRepository implements \Softtek\Sales\Api\SellerRepositoryInterface
{
    /**
     * @var \Softtek\Sales\Model\ResourceModel\Sellerorder
     */
    protected $sellerorder;

    public function __construct(
        Sellerorder $sellerorder
    )
    {
        $this->sellerorder = $sellerorder;
    }

    /**
     * {@inheritdoc}
     */
    public function getBySellerId($sellerId)
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $collectionOfOrders */
        $collectionOfOrders = $this->sellerorder->getSellerOrders($sellerId);
        return $collectionOfOrders;
    }
}
