<?php
/**
 * Purpletree_Marketplace SellerReview
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

use Magento\Customer\Model\Session as CustomerSession;
use Purpletree\Marketplace\Block\SellerReview as PurpletreeSellerReview;
use Purpletree\Marketplace\Model\ResourceModel\Reviews;
use Magento\Framework\Registry;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\View\Element\Template\Context;
use Purpletree\Marketplace\Model\ReviewsFactory;
use Purpletree\Marketplace\Model\ResourceModel\Seller;
use Purpletree\Marketplace\Helper\Data;
use Magento\Framework\App\RequestInterface;


class SellerReview extends PurpletreeSellerReview
{
    /**
     * Constructor
     *
     * @param Reviews $reviewsDetails
     * @param Registry $coreRegistry
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param Context $context
     * @param ReviewsFactory $reviewsCollectionFactory
     * @param Seller $storeDetails
     * @param RequestInterface $requestInterface
     * @param Data $dataHelper
     * @param array $data
     */
    public function __construct(
        Reviews $reviewsDetails,
        Registry $coreRegistry,
        CustomerRepositoryInterface $customerRepositoryInterface,
        Context $context,
        ReviewsFactory $reviewsCollectionFactory,
        Seller $storeDetails,
        Data $dataHelper,
        RequestInterface $request,
        CustomerSession $customer,
        array $data = []
    ) {
        $this->coreRegistry                 =       $coreRegistry;
        $this->reviewsDetails               =       $reviewsDetails;
        $this->customerRepositoryInterface  =       $customerRepositoryInterface;
        $this->reviewsCollectionFactory     =       $reviewsCollectionFactory;

        $this->storeDetails                 =       $storeDetails;
        $this->dataHelper                   =       $dataHelper;
        $this->request                      =       $request;
        $this->customer                     =       $customer;

        $storeUrl = $this->request->getParam('store');
        $sellerId=$this->storeDetails->storeIdByUrl($storeUrl);
        $this->coreRegistry->register('seller_Id', $sellerId);
        if ($this->customer->isLoggedIn()) {
            $this->coreRegistry->register('customer_Id', $this->customer->getCustomer()->getId());
        } else {
            $this->coreRegistry->register('customer_Id', '');
        }

        parent::__construct($reviewsDetails, $coreRegistry, $customerRepositoryInterface,  $context, $reviewsCollectionFactory, $data);
    }
}
