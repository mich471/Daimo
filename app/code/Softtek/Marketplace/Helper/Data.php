<?php
/**
 * Softtek_Marketplace SellerData
 *
 * @category    Softtek
 * @package     Softtek_Marketplace
 * @author      J. Abraham Serena <jorge.serena@softtek.com>
 * @copyright   © Softtek 2022. All rights reserved.
 */
namespace Softtek\Marketplace\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @const string
     */
    CONST SYSTEM_CONFIG_FORM_ENABLE = 'purpletree_marketplace/registartion_form/enabled';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Data Constructor
     *
     * @param \Purpletree\Marketplace\Model\SellerFactory
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Framework\Message\ManagerInterface
     */
    public function __construct(
        \Purpletree\Marketplace\Model\SellerFactory $sellerFactory,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->sellerFactory    =   $sellerFactory;
        $this->storeDetails     =   $storeDetails;
        $this->messageManager   =   $messageManager;
        $this->scopeConfig   =   $scopeConfig;
    }

    /**
     * Check is feature enable
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::SYSTEM_CONFIG_FORM_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }
}
