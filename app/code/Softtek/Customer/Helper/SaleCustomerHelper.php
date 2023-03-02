<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Softtek\Customer\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Sales\Model\Order;

class SaleCustomerHelper extends AbstractHelper
{

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context                      $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    )
    {
        $this->orders = [];
        $this->_orderCollectionFactory = $orderCollectionFactory;
        parent::__construct($context);
    }

    public function getCustomerOrders($customerId,$sellerId)
    {
        $collection = $this->_orderCollectionFactory->create();
        $this->orders = $collection
            ->addFieldToSelect('entity_id')
            ->addFieldToFilter('customer_id', $customerId)
            ->join(
                ['sellerorder' => $collection->getConnection()->getTableName('purpletree_marketplace_sellerorder')],
                'main_table.entity_id = sellerorder.order_id',
                ['seller_id']
            )
            ->addFieldToFilter('sellerorder.seller_id', $sellerId)
            ->setOrder('main_table.created_at','desc');

        return $this->orders->getData();
    }
}

