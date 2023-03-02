<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Softtek\Marketplace\Block;

use Magento\Customer\Block\Account\SortLinkInterface;
use Magento\Framework\View\Page\Config;

class RemoveSectionCustomer extends \Magento\Framework\View\Element\Html\Links

{

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Customer\Model\Session
     * @param array $data
     * @param Config $pageConfig
     */
    /**
     * @var Config
     */
    protected $pageConfig;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $sellercustom,
        \Magento\Customer\Model\Session $customerSession,
        Config $pageConfig,
        array $data = []
    ) {
        $this->sellercustom = $sellercustom;
        $this->customerSession = $customerSession;
        $this->pageConfig = $pageConfig;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getLinks()
    {
        $links = $this->_layout->getChildBlocks($this->getNameInLayout());
        $sortableLink = [];
        foreach ($links as $key => $link) {
            if ($link instanceof SortLinkInterface) {
                $sortableLink[] = $link;
                unset($links[$key]);
            }
        }

        $this->removeChild();
        usort($sortableLink, [$this, "compare"]);

        return array_merge($sortableLink, $links);
    }

    /**
     * Compare sortOrder in links.
     *
     * @param SortLinkInterface $firstLink
     * @param SortLinkInterface $secondLink
     * @return int
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function compare(SortLinkInterface $firstLink, SortLinkInterface $secondLink): int
    {
        return  $secondLink->getSortOrder() <=> $firstLink->getSortOrder();
    }

    public function removeChild(){
        if ($this->sellercustom->isSeller($this->getId())){
                $layoutContainer = $this->_layout->getBlock('customer_account_navigation');
                if ($layoutContainer) {
                    $layoutContainer->unsetChild('customer-account-navigation-product-reviews-link');
                    $layoutContainer->unsetChild('customer-account-navigation-orders-link');
                    $layoutContainer->unsetChild('customer-account-navigation-product-questions-link');
                    $layoutContainer->unsetChild('social-account');
                }
        }
    }
    public function getId() {
        return $this->customerSession->getId();
    }
}
