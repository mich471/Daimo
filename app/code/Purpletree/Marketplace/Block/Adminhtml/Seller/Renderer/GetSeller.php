<?php

/**
 * Purpletree_Marketplace GetSeller
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
 
namespace Purpletree\Marketplace\Block\Adminhtml\Seller\Renderer;
 
use Magento\Framework\DataObject;
 
class GetSeller extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        \Purpletree\Marketplace\Model\ResourceModel\Seller $sellercustom
    ) {
        $this->sellercustom = $sellercustom;
    }
 
    /**
     * get category name
     * @param  DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        if (null !== $row->getSellerId() && $row->getSellerId()!= '') {
            if ($this->sellercustom->getSellerNameBySellerId($row->getSellerId()) != '') {
                return $this->sellercustom->getSellerNameBySellerId($row->getSellerId());
            }
        }
        return '';
    }
}
