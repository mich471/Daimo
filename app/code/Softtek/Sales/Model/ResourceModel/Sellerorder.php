<?php

namespace Softtek\Sales\Model\ResourceModel;

class Sellerorder extends \Purpletree\Marketplace\Model\ResourceModel\Sellerorder
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    )
    {
        $this->orderCollectionFactory = $orderCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @param $sellerId
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSellerOrders($sellerId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from(
                ['mt' => $this->getMainTable()],
                ['order_id']
            )
            ->where('seller_id = ?', $sellerId);
        $all = $adapter->fetchCol($select);

        $collection = $this->orderCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', ['in', $all]);

        return $collection;
    }
}
