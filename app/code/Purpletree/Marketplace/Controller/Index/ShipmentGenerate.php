<?php
/**
 * Purpletree_Marketplace ShipmentGenerate
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

namespace Purpletree\Marketplace\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use \Magento\Customer\Model\Session as CustomerSession;

class ShipmentGenerate extends \Magento\Framework\App\Action\Action
{
    /**
     * @param Context
     * @param \Magento\Customer\Model\Session
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Framework\Controller\Result\ForwardFactory
     * @param \Purpletree\Marketplace\Helper\Data
     * @param \Magento\Framework\Registry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        CustomerSession $customer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Magento\Framework\Registry $coreRegistry,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory        =       $resultPageFactory;
        $this->customer                 =       $customer;
        $this->storeManager             =       $storeManager;
        $this->dataHelper               =       $dataHelper;
        $this->storeDetails                 =       $storeDetails;
        $this->resultForwardFactory     =       $resultForwardFactory;
        $this->coreRegistry             =       $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Customer order history
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $customerId=$this->customer->getCustomer()->getId();
        $sellerId=$this->storeDetails->isSeller($customerId);
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        $manageOrder=$this->dataHelper->getGeneralConfig('general/allow_seller_manage_order');
        if (!$this->customer->isLoggedIn()) {
            $this->customer->setAfterAuthUrl($this->storeManager->getStore()->getCurrentUrl());
            $this->customer->authenticate();
        }
        if ($sellerId=='' || !$moduleEnable || !$manageOrder) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        $id  = $this->getRequest()->getParam('order_id');
        $this->coreRegistry->register('id', $id);
        $this->_resultPage = $this->resultPageFactory->create();
        
        $this->_resultPage->getConfig()->getTitle()->set(__('New Shipment'));
        return $this->_resultPage;
    }
}
