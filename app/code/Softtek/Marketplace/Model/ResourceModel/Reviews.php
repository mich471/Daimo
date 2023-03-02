<?php
namespace Softtek\Marketplace\Model\ResourceModel;

use Purpletree\Marketplace\Model\ResourceModel\Reviews as MpReviews;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Softtek\Marketplace\Model\ResourceModel\OrderReview\CollectionFactory;

class Reviews extends MpReviews
{
    /**
     * @var DateTime
     */
    protected $_date;

    /**
     * @var CollectionFactory
     */
    protected $_orderReviewCollection;

    /**
     * constructor
     *
     * @param DateTime $date
     * @param Context $context
     * @param CollectionFactory $orderReviewCollection
     */
    public function __construct(
        DateTime $date,
        Context $context,
		CollectionFactory $orderReviewCollection
    ) {
        $this->_date = $date;
		$this->_orderReviewCollection = $orderReviewCollection;

        parent::__construct($date, $context);
    }

    /**
     * Geting Reviews Count
     *
     * @return Reviews Count
     */
    public function getReviewsCount($sellerId)
    {
        $col = $this->_orderReviewCollection->create();
		$soTable = $col->getConnection()->getTableName('purpletree_marketplace_sellerorder');
        $col
			->join(
                    ['seller_order' => $soTable],
                    'seller_order.order_id = main_table.order_id',
                    ['seller_id']
                )
			->getSelect()
				->where('seller_order.seller_id = ' . $sellerId)
				->where('main_table.approved = 1');
        return count($col);
    }
}
