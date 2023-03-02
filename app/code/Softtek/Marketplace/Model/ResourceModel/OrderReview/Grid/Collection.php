<?php
namespace Softtek\Marketplace\Model\ResourceModel\OrderReview\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{
    /**
     * Init Select for Relation Grid
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $subSellerOrders = new \Zend_Db_Expr(sprintf("(SELECT order_id, MIN(seller_id) as seller_id FROM %s GROUP BY order_id)",
            $this->getTable('purpletree_marketplace_sellerorder')));

        $this->getSelect()
            ->join(
                ['order' => $this->getTable('sales_order')],
                'order.entity_id = main_table.order_id',
                ['increment_id']
            )->join(
                ['seller_order' => $subSellerOrders],
                'seller_order.order_id = main_table.order_id',
                []
            )->join(
                ['seller_store' => $this->getTable('purpletree_marketplace_stores')],
                'seller_store.seller_id = seller_order.seller_id',
                ['store_name']
            );

        return $this;
    }
}
