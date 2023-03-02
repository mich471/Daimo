<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_AdvancedReports
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\AdvancedReports\Api;

/**
 * Interface OrderdetailInterface
 * @package Lof\AdvancedReports\Api
 * @api
 */
interface OrderdetailInterface
{
	
	/**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\AdvancedReports\Api\Data\OrderdetaildataInterface
     */
   public function getOrderDetail(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}