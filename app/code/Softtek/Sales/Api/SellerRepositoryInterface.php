<?php

namespace Softtek\Sales\Api;

interface SellerRepositoryInterface
{
    /**
     * Enables an administrative user to return information for a specified cart.
     *
     * @param int $sellerId
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface
     */
    public function getBySellerId($sellerId);
}
