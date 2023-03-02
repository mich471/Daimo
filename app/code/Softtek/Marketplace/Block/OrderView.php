<?php
/**
 * Purpletree_Marketplace OrderView
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
 * It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
 *
 * @category    Purpletree
 * @package     Purpletree_Marketplace
 * @author      Purpletree Software
 * @copyright   Copyright (c) 2020
 * @license     https://www.purpletreesoftware.com/license.html
 */

namespace Softtek\Marketplace\Block;

use Purpletree\Marketplace\Block\OrderView as MarketplaceOrderView;
use Magento\Sales\Model\Order\Status\History;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\Registry;
use Magento\Directory\Model\Currency;
use Purpletree\Marketplace\Helper\Data;
use Purpletree\Marketplace\Model\ResourceModel\Sellerorder;
use Purpletree\Marketplace\Model\ResourceModel\Sellerorderinvoice;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory as HistoryCollection;

/**
 * Sales order history block
 */
class OrderView extends MarketplaceOrderView
{
    /**
     * @var HistoryCollection
     */
    protected $historyCollectionFactory;

    /**
     * @param HistoryCollection $historyCollectionFactory
     */
    public function __construct(
        Context $context,
        Order $order,
        CountryFactory $countryFactory,
        Registry $coreRegistry,
        Currency $currency,
        Data $dataHelper,
        Sellerorder $sellerOrder,
        Sellerorderinvoice $sellerOrderInvoice,
        CollectionFactory $statusCollectionFactory,
        PriceHelper $priceHelper,
        HistoryCollection $historyCollectionFactory,
        array $data = []
    ) {
        $this->historyCollectionFactory = $historyCollectionFactory;

        parent::__construct($context, $order, $countryFactory, $coreRegistry, $currency, $dataHelper, $sellerOrder, $sellerOrderInvoice, $statusCollectionFactory, $priceHelper, $data);
    }

    /**
     * Get Order object
     * @return Order Object
     */
    public function getOrder()
    {
        $orderId    = $this->getOrderId();
        return $this->order->load($orderId);
    }

    /**
     * Customer Notification Applicable check method
     *
     * @param  History $history
     * @return bool
     */
    public function isCustomerNotificationNotApplicable(History $history)
    {
        return $history->isCustomerNotificationNotApplicable();
    }

    /**
     * Return collection of order status history items.
     *
     * @return HistoryCollection
     */
    public function getStatusHistoryCollection()
    {
        $collection = $this->historyCollectionFactory->create()->setOrderFilter($this->getOrder())
            ->setOrder('created_at', 'desc')
            ->setOrder('entity_id', 'desc');
        $collection->getSelect()->where("sm_is_message != 1 OR sm_is_message IS NULL");
        foreach ($collection as $status) {
            $status->setOrder($this->getOrder());
        }
        return $collection;
    }

    /**
     * Return collection of order messages.
     *
     * @return HistoryCollection
     */
    public function getStatusMessagesCollection()
    {
        $collection = $this->historyCollectionFactory->create()->setOrderFilter($this->getOrder())
            ->addFieldToFilter('sm_is_message', ['eq'=> 1])
            ->setOrder('created_at', 'desc')
            ->setOrder('entity_id', 'desc');
        foreach ($collection as $status) {
            $status->setOrder($this->getOrder());
        }
        return $collection;
    }

    /**
     * Get Available Statuses By Current Order
     *
     * @return Currency Data
     */
    public function getAvailableStatusesByCurrentOrder()
    {
        $statuses = $this->statusCollectionFactory->create();
        $currentStatus = $this->getOrder()->getStatus();
        $allStatuses = [];
        foreach ($statuses as $status) {
            $allStatuses[$status->getStatus()] = $status->getLabel();
        }
        $availableStatuses = [];
        if ($allStatuses[$currentStatus]) {
            $availableStatuses[$currentStatus] = $allStatuses[$currentStatus];
        }

        switch ($currentStatus) {
            case "payment_review":
                $availableStatuses["cancelamento_solicitado"] = $allStatuses["cancelamento_solicitado"];
                break;
            case "processing":
                $availableStatuses["notafiscalemitida"] = $allStatuses["notafiscalemitida"];
                $availableStatuses["cancelamento_solicitado"] = $allStatuses["cancelamento_solicitado"];
                break;
            case "notafiscalemitida":
                $availableStatuses["pickingpacking"] = $allStatuses["pickingpacking"];
                $availableStatuses["cancelamento_solicitado"] = $allStatuses["cancelamento_solicitado"];
                break;
            case "pickingpacking":
                $availableStatuses["shipping"] = $allStatuses["shipping"];
                $availableStatuses["readytopickorship"] = $allStatuses["readytopickorship"];
                $availableStatuses["cancelamento_solicitado"] = $allStatuses["cancelamento_solicitado"];
                break;
            case "shipping":
                $availableStatuses["entregue"] = $allStatuses["entregue"];
                $availableStatuses["cancelamento_solicitado"] = $allStatuses["cancelamento_solicitado"];
                break;
            case "readytopickorship":
                $availableStatuses["entregue"] = $allStatuses["entregue"];
                $availableStatuses["cancelamento_solicitado"] = $allStatuses["cancelamento_solicitado"];
                break;
            case "cancelamento_solicitado":
                $availableStatuses["entregue"] = $allStatuses["entregue"];
                break;
        }

        $availableStatusesWithFormat = [];
        foreach ($availableStatuses as $sk => $sv) {
            $availableStatusesWithFormat[] = [
                'value' => $sk,
                'label' => $sv
            ];
        }

        return $availableStatusesWithFormat;
    }
}
