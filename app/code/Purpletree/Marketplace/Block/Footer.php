<?php
/**
 * Purpletree_Marketplace Footer
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
namespace Purpletree\Marketplace\Block;

class Footer extends \Magento\Framework\View\Element\Html\Link
{
    protected $_template = 'Purpletree_Marketplace::link.phtml';
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Registry $coreRegistry,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_dataHelper = $dataHelper;
        $this->storeDetails             =       $storeDetails;
        $this->customerSession = $customerSession;
        parent::__construct(
            $context,
            $data
        );
    }
    
    public function isEnabled()
    {
        return $this->_dataHelper->getGeneralConfig('general/enabled');
    }
    
    public function isVisible()
    {
        return $this->_dataHelper->getGeneralConfig('manage_links/footer_enable');
    }
    
    public function getLabel()
    {
        if (null !== $this->_coreRegistry->registry('selleridd')) {
              return "My Store";
        }
        return $this->_dataHelper->getGeneralConfig('manage_links/footer_text');
    }
    public function getHref()
    {
        if (null !== $this->_coreRegistry->registry('selleridd')) {
              return "marketplace/index/sellers/";
        }
        return 'marketplace/index/becomeseller/';
    }
    public function _prepareLayout()
    {
        if (null !== $this->_coreRegistry->registry('selleridd')) {
            //no action
        } else {
            if ($this->customerSession->isLoggedIn()) {
                $customerId = $this->customerSession->getCustomer()->getId();
                $selleridd = $this->storeDetails->isSeller($customerId);
                if (isset($selleridd) && $selleridd !='') {
                    $this->_coreRegistry->register('selleridd', $selleridd);
                }
            }
        }
        return parent::_prepareLayout();
    }
    public function getBrowseLabel()
    {
        return $this->_dataHelper->getGeneralConfig('manage_links/sellers_link_label');
    }
    public function getBrowseHref()
    {
        return $this->_dataHelper->getGeneralConfig('manage_links/sellers_link_seo');
    }
}
