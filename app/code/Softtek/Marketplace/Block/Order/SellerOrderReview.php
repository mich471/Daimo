<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Softtek\Marketplace\Block\Order;

use \Magento\Framework\App\ObjectManager;

/**
 * Sales order history block
 *
 * @api
 * @since 100.0.2
 */
class SellerOrderReview extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'Softtek_Marketplace::order_review/seller_history.phtml';

    /**
     * @var \Softtek\Marketplace\Model\ResourceModel\OrderReview\CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected $orders;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Softtek\Marketplace\Model\ResourceModel\OrderReview\CollectionFactory $orderCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Softtek\Marketplace\Model\ResourceModel\OrderReview\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        array $data = []
    ) {
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_orderConfig = $orderConfig;
        parent::__construct($context, $data);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Order Reviews'));
    }

    /**
     * Get customer orders
     *
     * @return bool|\Softtek\Marketplace\Model\ResourceModel\OrderReview\Collection
     */
    public function getOrders()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->orders) {

            $this->orders = $this->_orderCollectionFactory->create();
            $this->orders->addFieldToSelect(
                '*'
            )->join(
                ['orders' => $this->orders->getConnection()->getTableName('sales_order')],
                'main_table.order_id = orders.entity_id',
                ['increment_id', 'status', 'order_created_at' => 'created_at']
            )->join(
                ['sellerorders' => $this->orders->getConnection()->getTableName('purpletree_marketplace_sellerorder')],
                'main_table.order_id = sellerorders.order_id',
                ['seller_id']
            )->addFieldToFilter(
                'sellerorders.seller_id',
                ['eq' => $customerId]
            )->addFieldToFilter(
                'main_table.approved',
                ['eq' => 1]
            )->setOrder(
                'main_table.created_at',
                'desc'
            );
        }
        return $this->orders;
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getOrders()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'sales.order.history.pager'
            )->setCollection(
                $this->getOrders()
            );
            $this->setChild('pager', $pager);
            $this->getOrders()->load();
        }
        return $this;
    }

    /**
     * Get Pager child block output
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get order view URL
     *
     * @param object $order
     * @return string
     */
    public function getViewUrl($order)
    {
        return $this->getUrl('sales/order/view', ['order_id' => $order->getOrderId()]);
    }
}
