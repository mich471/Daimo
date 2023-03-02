<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Softtek\RemoveDescription\Block;

use Purpletree\Marketplace\Block\Seller;

class RemoveDescription extends \Magento\Framework\View\Element\Template
{
    /**
     * @var set block to templates
     */
    protected $_template = "Softtek_RemoveDescription::removeDecription.phtml";

    /**
     * @var array
     */
    protected $storeData;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $sessionCustomer;

    /**
     * Constructor
     * @param \Magento\Framework\Registry
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->storeDetails=$storeDetails;
        $this->sessionCustomer = $session;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;

    }

    /**
     * Get seller store data
     * @return array
     */
    public function getStoreData(){
        if (is_null($this->storeData)){
            $this->storeData = $this->storeDetails->getStoreDetails($this->getSellerId());
        }

        return $this->storeData;
    }

    public function getDescription()
    {
        return $this->getStoreData();
    }

    public function getPolity()
    {
        return $this->getStoreData();
    }
    public function getShippingPolity()
    {
        return $this->getStoreData();
    }
    public function getStoreReviews()
    {
        return $this->getStoreData();
    }

    /**
     * Get Seller Id and store url
     *
     * @return Seller Id
     */
    public function getSellerId()
    {
        $storeUrl = $this->coreRegistry->registry('store_url');
        $sellerId = $this->storeDetails->storeIdByUrl($storeUrl);
        return $sellerId;
    }
}
