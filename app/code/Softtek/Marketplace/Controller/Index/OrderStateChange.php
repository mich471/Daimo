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

class OrderStateChange extends MarketplaceChangeSellerStatus
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
        PageFactory $resultPageFactory,
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
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($context, $customer, $storeManager, $storeDetails, $resultForwardFactory, $sellerorderFactory, $sellerorder, $sellerorderCollectionFactory, $dataHelper);
    }

    /**
     * Customer order history
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $processingState = \Magento\Sales\Model\Order::STATE_PROCESSING;
        $concluidoStatus = 'entrega_confirmada';

        try{
            $id  = $this->getRequest()->getParam('id_order');
            $order = $this->orderRepository->get($id);
            $order->setStatus($concluidoStatus)->setState($processingState);
            $this->orderRepository->save($order);

            $entity_ids  = $this->_sellerorder->getEntityIdfromOrderId2($id);
            foreach ($entity_ids as $idd) {
                $sellerorder = $this->_sellerorderFactory->create();
                $sellerorder->load($idd['entity_id']);
                $sellerorder->setOrderStatus($concluidoStatus);
                $sellerorder->save();
                break;
            }

            $history = $order->addStatusHistoryComment(__('Delivery confirmed by the customer'), $order->getStatus());
            $history->setIsVisibleOnFront(true);
            $history->setIsCustomerNotified(true);
            $history->save();

            $this->messageManager->addSuccess(__('Pedido Confirmado'));
        } catch (NoSuchEntityException $ex) {
            // error Happen
        }
        return $this->resultPageFactory->create();

    }
}
