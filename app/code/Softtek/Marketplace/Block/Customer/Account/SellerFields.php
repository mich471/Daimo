<?php
/**
 * Purpletree_Marketplace Seller
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

namespace Softtek\Marketplace\Block\Customer\Account;

use Magento\Catalog\Model\Product\AttributeSet\Options;
use Magento\Directory\Model\Config\Source\Country;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Purpletree\Marketplace\Block\Seller as MarketplaceSeller;
use Purpletree\Marketplace\Model\ResourceModel\Seller;
use Softtek\Marketplace\Block\AttributeRepositoryInterface;
use Softtek\Marketplace\Block\StoreManagerInterface;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Directory\Model\ResourceModel\Country\Collection;

class SellerFields extends MarketplaceSeller
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * Constructor
     *
     * @param Country
     * @param Options
     * @param AttributeRepositoryInterface
     * @param Seller
     * @param Registry
     * @param StoreManagerInterface
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param Collection $countryCollection
     * @param Context
     * @param array $data
     */
    public function __construct(
        Country $countryHelper,
        Options $option,
        CountryFactory $countryFactory,
        Seller $storeDetails,
        Registry $coreRegistry,
        ProductMetadataInterface $productMetadataInterface,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        Collection $countryCollection,
        Context $context,
        array $data = []
    ) {
        $this->countryFactory = $countryFactory;
        $this->countryHelper = $countryHelper;
        $this->coreRegistry = $coreRegistry;
        $this->option = $option;
        $this->storeDetails = $storeDetails;
        $this->_productMetadataInterface = $productMetadataInterface;
        $this->session = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->_countryCollection = $countryCollection;

        parent::__construct($countryHelper, $option, $countryFactory, $storeDetails, $coreRegistry, $productMetadataInterface, $context, $data);
    }

    /**
     * Seller Form Data
     *
     * @return array
     */
    public function getSellerFormData()
    {
        return $this->session->getSellerFormData(true);
    }

    /**
     * Store Details
     *
     * @return array
     */
    public function getStoreDetails()
    {
        $data = $this->getSellerFormData();
        if (!$data && $this->session->isLoggedIn()) {
            $customerId = $this->session->getCustomer()->getId();
            $data = $this->storeDetails->getStoreDetails($customerId);
        }

        if (!$data) {
            $data = [];
        }

        return $data;
    }

    /**
     * Get Seller Id
     *
     * @return Seller Id
     */
    public function getSellerId()
    {
        return $this->coreRegistry->registry('seller_id');
    }

    /**
     * Get Customer Seller or not
     *
     * @return Seller
     */
    public function getIsSeller()
    {
        $customer = $this->customerRepository->getById($this->getSellerId());
        if (!empty($customer->getCustomAttribute('is_seller'))) {
            return $customer->getCustomAttribute('is_seller')->getValue();
        } else {
            return 0;
        }
    }

    /**
     * Get output
     *
     * @return string
     */
    public function _toHtml()
    {
        $actionName = $this->getRequest()->getActionName();
        if (
            $this->getRequest()->getParam('ut') != 'seller'
            && $actionName != 'becomeseller'
        ) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * Country List
     *
     * @return Country List
     */
    public function getCountry()
    {
        return $this->_countryCollection->loadByStore()->loadData()->toOptionArray();
    }
}
