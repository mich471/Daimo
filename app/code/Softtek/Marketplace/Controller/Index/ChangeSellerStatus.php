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
 * @copyright   Copyright (c) 2017
 * @license     https://www.purpletreesoftware.com/license.html
 */

namespace Softtek\Marketplace\Controller\Index;

use Purpletree\Marketplace\Controller\Index\ChangeSellerStatus as MarketplaceChangeSellerStatus;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Store\Model\StoreManagerInterface;
use Purpletree\Marketplace\Model\ResourceModel\Seller;
use Magento\Framework\Controller\Result\ForwardFactory;
use Purpletree\Marketplace\Model\SellerorderFactory;
use Purpletree\Marketplace\Model\ResourceModel\Sellerorder;
use Purpletree\Marketplace\Model\ResourceModel\Sellerorder\CollectionFactory;
use Purpletree\Marketplace\Helper\Data;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;

class ChangeSellerStatus extends MarketplaceChangeSellerStatus
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var OrderCommentSender
     */
    protected $orderCommentSender;

    /**
     * @param Context $context
     * @param Session $customer
     * @param StoreManagerInterface $storeManager,
     * @param Seller $storeDetails,
     * @param ForwardFactory $resultForwardFactory,
     * @param SellerorderFactory $sellerorderFactory,
     * @param Sellerorder $sellerorder,
     * @param CollectionFactory $sellerorderCollectionFactory,
     * @param Data $dataHelper
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderCommentSender $orderCommentSender
     */
    public function __construct(
        Context $context,
        CustomerSession $customer,
        StoreManagerInterface $storeManager,
        Seller $storeDetails,
        ForwardFactory $resultForwardFactory,
        SellerorderFactory $sellerorderFactory,
        Sellerorder $sellerorder,
        CollectionFactory $sellerorderCollectionFactory,
        Data $dataHelper,
        OrderRepositoryInterface $orderRepository,
        OrderCommentSender $orderCommentSender
    ) {
        $this->customer                      = $customer;
        $this->_sellerorder                 = $sellerorder;
        $this->storeManager                  = $storeManager;
        $this->dataHelper                    = $dataHelper;
        $this->storeDetails                  = $storeDetails;
        $this->_sellerorderCollectionFactory = $sellerorderCollectionFactory;
        $this->_sellerorderFactory           = $sellerorderFactory;
        $this->resultForwardFactory          = $resultForwardFactory;
        $this->orderRepository = $orderRepository;
        $this->orderCommentSender = $orderCommentSender;

        parent::__construct($context, $customer, $storeManager, $storeDetails, $resultForwardFactory, $sellerorderFactory, $sellerorder, $sellerorderCollectionFactory, $dataHelper);
    }

    /**
     * Customer order history
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $customerId = $this->customer->getCustomer()->getId();
        $sellerId = $this->storeDetails->isSeller($customerId);
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        if (!$this->customer->isLoggedIn()) {
            $this->customer->setAfterAuthUrl($this->storeManager->getStore()->getCurrentUrl());
            $this->customer->authenticate();
        }
        $sellerStatus  = $this->getRequest()->getParam('seller_status');
        $id  = $this->getRequest()->getParam('order_id');
        if (!$id || $sellerId=='' || !$moduleEnable) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        $sellerorderr = $this->getOrderCollection($id);
        if (!$sellerorderr) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }

        $historyData = $this->getRequest()->getPost('history');
        $notify = $historyData['is_customer_notified'] ?? false;
        $visible = $historyData['is_visible_on_front'] ?? false;
        if (empty($historyData['comment'])) {
            $historyData['comment'] = __('Order status changed');
        }
        try {
            $order = $this->orderRepository->get($id);
            $previousOrderStatus = $order->getStatus();
            $order->setStatus($sellerStatus);
            $history = $order->addStatusHistoryComment($historyData['comment'], $sellerStatus);
            $history->setIsVisibleOnFront($visible);
            $history->setIsCustomerNotified($notify);
            $history->save();
            $order->save();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            return $this->_redirect('marketplace/index/orderview/order_id/'.$id);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('We cannot add order comment.'));
            return $this->_redirect('marketplace/index/orderview/order_id/'.$id);
        }

        $entity_ids  = $this->_sellerorder->getEntityIdfromOrderId($customerId, $id);

        foreach ($entity_ids as $idd) {
            $sellerorder = $this->_sellerorderFactory->create();
            $sellerorder->load($idd['entity_id']);
            $sellerorder->setOrderStatus($sellerStatus);
            $sellerorder->save();
        }

        $ignoredStatusesToNotify = ['pickingpacking'];

        if ($notify && (!in_array($sellerStatus, $ignoredStatusesToNotify) || $previousOrderStatus == $sellerStatus)) {
            $comment = trim(strip_tags($historyData['comment']));
            /** @var OrderCommentSender $orderCommentSender */
            $this->orderCommentSender->send($order, $notify, $comment);
        }

        $this->messageManager->addSuccess(__('Status alterado com sucesso.'));

        return $this->_redirect('marketplace/index/orderview/order_id/'.$id);
    }
}
