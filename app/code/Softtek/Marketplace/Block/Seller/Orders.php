<?php
namespace Softtek\Marketplace\Block\Seller;

use Magento\Framework\App\ObjectManager;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;
use Purpletree\Marketplace\Block\Orders as MarketplaceOrders;

/**
 * Sales order history block
 */
class Orders extends MarketplaceOrders
{
    /**
     * @return CollectionFactoryInterface
     *
     * @deprecated
     */
    private function getOrderCollectionFactory()
    {
        if ($this->orderCollectionFactory === null) {
            //Bad practice from Magento core
            $this->orderCollectionFactory = ObjectManager::getInstance()->get(CollectionFactoryInterface::class);
        }
        return $this->orderCollectionFactory;
    }

    /**
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getOrders()
    {
        $collectiossn = $this->_sellerorderCollectionFactory->create();
                $sellerId   = (int) $this->coreRegistry->registry('sellerId');
        foreach ($collectiossn as $dddd) {
            if ($sellerId == $dddd->getSellerId()) {
                $orderids[] = $dddd->getOrderId();
            }
        }

        if (!$this->orders) {
            if ($this->getRequest()->isAjax()) {
                $data = $this->getRequest()->getPostValue();
				$fromDate  =  (isset($data['from']) && $data['from']!='')?date('Y-m-d', strtotime($data['from'])).' 00:00:00':'';
				$toDate  = (isset($data['report_to']) && $data['report_to']!='')?date('Y-m-d', strtotime($data['report_to'])).' 23:59:59':'';
                $this->orders = $this->getOrdersAjax($fromDate, $toDate, $orderids);
            } else {
                if (!empty($orderids)) {
                    $this->orders = $this->getOrderCollectionFactory()->create()->addFieldToSelect(
                        '*'
                    )->addAttributeToFilter('entity_id', ['in' => $orderids])->setOrder(
                        'created_at',
                        'desc'
                    );
                }
            }
        }
        return $this->orders;
    }
    public function getOrdersAjax($fromDate, $toDate, $orderids = [])
    {
        if ($fromDate =='' && $toDate =='') {
            $orders = $this->getOrderCollectionFactory()->create()->addFieldToSelect(
                '*'
            )->addAttributeToFilter('entity_id', ['in' => $orderids])->setOrder(
                'created_at',
                'desc'
            );
        } elseif ($fromDate == '') {
            $orders = $this->getOrderCollectionFactory()->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'created_at',
                [
                'lt'=>$toDate
                ]
            )->addAttributeToFilter('entity_id', ['in' => $orderids])->setOrder(
                'created_at',
                'desc'
            );
        } elseif ($toDate == '') {
            $orders = $this->getOrderCollectionFactory()->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'created_at',
                [
                'gt'=>$fromDate
                ]
            )->addAttributeToFilter('entity_id', ['in' => $orderids])->setOrder(
                'created_at',
                'desc'
            );
        } else {
            $orders = $this->getOrderCollectionFactory()->create()->addFieldToSelect(
                '*'
            )->addAttributeToFilter(
                'created_at',
                [
                    'from'=>$fromDate,
                    'to'=>$toDate
                 ]
            )->addAttributeToFilter('entity_id', ['in' => $orderids])->setOrder(
                'created_at',
                'desc'
            );
        }
        return $orders;
    }
}
