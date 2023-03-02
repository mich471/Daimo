<?php

/**
 * @Author: Ha Manh
 * @Date:   2020-12-08 08:29:17
 * @Last Modified by:   Alex Dong
 * @Last Modified time: 2021-06-04 10:45:54
 */

namespace Softtek\Marketplace\Controller\Cancelorder;

use Magepow\CancelOrder\Controller\Cancelorder\Index as CancelOrderIndex;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Model\Order;
use Magento\Customer\Model\Session;
use Magepow\CancelOrder\Helper\Data;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

class Index extends CancelOrderIndex
{
    /**
     * @var TransportBuilder
     */
    private   $transportBuilder;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var PriceHelper
     */
    protected $priceHelper;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var OrderCollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param PriceHelper $priceHelper
     * @param TransportBuilder $transportBuilder
     * @param PageFactory $resultPageFactory
     * @param Order $order
     * @param Session $customerSession
     * @param Data $helper
     * @param CollectionFactory $collectionFactory
     * @param OrderCollectionFactory $orderCollectionFactory
     */
    public function __construct(
        Context $context,
        PriceHelper $priceHelper,
        TransportBuilder $transportBuilder,
        PageFactory $resultPageFactory,
        Order $order,
        Session $customerSession,
        Data $helper,
        CollectionFactory $collectionFactory,
        OrderCollectionFactory $orderCollectionFactory
    ) {
        $this->priceHelper       = $priceHelper;
        $this->resultPageFactory = $resultPageFactory;
        $this->_order            = $order;
        $this->_customerSession  = $customerSession;
        $this->transportBuilder  = $transportBuilder;
        $this->helper            = $helper;
        $this->collectionFactory = $collectionFactory;
        $this->_orderCollectionFactory = $orderCollectionFactory;

        return parent::__construct($context, $priceHelper, $transportBuilder, $resultPageFactory, $order, $customerSession, $helper, $collectionFactory);
    }

    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $orderId = $this->getRequest()->getParam('orderid');
        $order = $this->_order->load($orderId);
        $productId = [];
        foreach ($order->getAllItems() as $item) {
            $productId[] = $item->getProductId();
        }
        $productCollection = $this->collectionFactory->create();
        $productCollection->addAttributeToSelect('*')->addFieldToFilter('entity_id', array('in' => $productId));
            $products = [];
            foreach ($productCollection as $product) {
                $products[] = $product;

            }
        $post['collectionProduct'] = $products;
        if($order->canCancel()){
            $order->cancel();
            $order->save();
            $this->messageManager->addSuccess(__('O pedido foi cancelado com sucesso.'));

            if(!$this->helper->getEmailSender()) {
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }

            $customerData = $this->_customerSession->getCustomer();
            $post['store_name'] = $order->getStore()->getName();
            $post['site_name'] = $order->getStore()->getWebsite()->getName();
            $post['entity_id'] = $order->getEntity_id();
            $post['base_grand_total'] = $this->priceHelper->currency($order->getBase_grand_total(), true, false);
            $post['created_at'] = $order->getCreated_at();
            $post['customer_lastname'] = $order->getCustomer_lastname();
            $post['orderid'] = $order->getIncrement_id();

            $senderName = $customerData->getName();
            $senderEmail = $customerData->getEmail();
            $sender = [
                'name' => $senderName,
                'email' => $this->helper->getEmailSender(),
                ];

            $sellerEmail = "";
            $orderCollection = $this->_orderCollectionFactory->create();
            $orderCollection->addFieldToSelect('entity_id')
                ->join(
                    ['sellerorder' => $orderCollection->getConnection()->getTableName('purpletree_marketplace_sellerorder')],
                    'main_table.entity_id = sellerorder.order_id',
                    ['seller_id']
                )
                ->join(
                    ['seller' => $orderCollection->getConnection()->getTableName('customer_entity')],
                    'sellerorder.seller_id = seller.entity_id',
                    ['email']
                )
                ->addFieldToFilter('main_table.entity_id', $order->getId());
            $orderCollection->load();
            if (count($orderCollection)) {
                $firstRecord = $orderCollection->getFirstItem();
                $sellerEmail = $firstRecord->getEmail();
            }

            $transportBuilder = $this->transportBuilder->setTemplateIdentifier('cancel_order_email_template')
            ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
            ->setTemplateVars($post)
            ->setFrom($sender)
            ->addTo($senderEmail);
            if ($sellerEmail) {
                $transportBuilder->addCc($sellerEmail);
            }
            $transport = $transportBuilder->getTransport();
            $transport->sendMessage();
        } else {
            $this->messageManager->addError(__('Order cannot be canceled.'));
        }
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
