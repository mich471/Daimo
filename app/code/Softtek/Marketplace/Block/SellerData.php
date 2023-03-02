<?php
/**
 * Softtek_Marketplace SellerData
 *
 * @category    Softtek
 * @package     Softtek_Marketplace
 * @author      J. Abraham Serena <jorge.serena@softtek.com>
 * @copyright   © Softtek 2022. All rights reserved.
 */
namespace Softtek\Marketplace\Block;

class SellerData extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * SellerData Constructor
     * 
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Customer\Model\Session
     * @param \Magento\Customer\Api\CustomerRepositoryInterface
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        array $data = []
    ) {
        $this->customerSession      =       $customerSession;
        $this->customerRepository   =       $customerRepository;
        $this->storeDetails         =       $storeDetails;
        
        parent::__construct($context, $data);
    }
   
    public function getCurrentUrl()
    {
        return $this->getRequest()->getActionName();
    }
    
    /**
     * Get Customer Id
     *
     * @return Customer Id
     */
    public function getCustomerId()
    {
        $customerId = $this->customerSession->getCustomerId();
        return $customerId;
    }
    
    /**
     * Get Seller
     *
     * @return Seller
     */
    public function isSeller()
    {
        $seller = $this->storeDetails->isSeller($this->getCustomerId());
        return $seller;
    }

    /**
     * Get Seller
     *
     * @return Seller Approve
     */
    public function isSellerApprove()
    {
        $seller = $this->storeDetails->isSellerApprove($this->getCustomerId());
        return $seller;
    }
    
    public function getIsSeller()
    {
        $customer = $this->customerRepository->getById($this->getCustomerId());
        if (!empty($customer->getCustomAttribute('is_seller'))) {
            return $customer->getCustomAttribute('is_seller')->getValue();
        } else {
            return 0;
        }
    } 
}
