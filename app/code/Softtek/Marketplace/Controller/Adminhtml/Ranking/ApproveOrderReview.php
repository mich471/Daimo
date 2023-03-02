<?php
namespace Softtek\Marketplace\Controller\Adminhtml\Ranking;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Softtek\Marketplace\Model\ResourceModel\OrderReview\CollectionFactory;
use Softtek\Marketplace\Model\OrderReviewFactory;

/**
 * Approve Order Review
 */
class ApproveOrderReview extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var CollectionFactory
     */
    protected $orderReviewCollection;

    /**
     * @var OrderReview
     */
    protected $orderReview;

    /**
     * ApproveOrderReview constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param CollectionFactory $orderReviewCollection
     * @param OrderReviewFactory $orderReview
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CollectionFactory $orderReviewCollection,
        OrderReviewFactory $orderReview
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->orderReviewCollection = $orderReviewCollection;
        $this->orderReview = $orderReview;

        parent::__construct($context);
    }

    /**
     * Execute mass action
     * @return ResultInterface
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$orderId) {
            $this->messageManager->addErrorMessage(
                __('No orders selected')
            );
            return $resultRedirect->setPath('softtek_marketplace/ranking/index');
        }
        try {
            $orderReview = $this->orderReview->create();
            $orderReview->load($orderId,'order_id');
            $orderReview->setApproved(1);
            $orderReview->save();
            $this->messageManager->addSuccessMessage(__('Order review has been updated.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Failed to update order review.'));
        }

        return $resultRedirect->setPath('softtek_marketplace/ranking/index');
    }
}
