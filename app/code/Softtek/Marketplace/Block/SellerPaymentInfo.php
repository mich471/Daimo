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

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Purpletree\Marketplace\Model\ResourceModel\Seller;

class SellerPaymentInfo extends Template
{
    /**
     * SellerPaymentInfo Constructor
     *
     * @param Context $context
     * @param Session $customerSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        Seller $storeDetails,
        array $data = []
    ) {
        $this->session = $customerSession;
        $this->storeDetails = $storeDetails;
        parent::__construct($context, $data);
    }

    /**
     * Seller Form Data
     *
     * @return array
     */
    public function getSellerPaymentFormData()
    {
        return $this->session->getSellerPaymentFormData(true);
    }

    /**
     * Store Details
     *
     * @return array
     */
    public function getStoreDetails()
    {
        $data = $this->getSellerPaymentFormData();
        if (!$data && $this->session->isLoggedIn()) {
            $customerId = $this->session->getCustomer()->getId();
            $data = $this->storeDetails->getStoreDetails($customerId);
        }

        if (!$data) {
            $data = [];
        }

        return $data;
    }
}
