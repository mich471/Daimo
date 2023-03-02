<?php
/**
 * Purpletree_Marketplace Router
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
namespace Purpletree\Marketplace\Controller;
 
/**
 * Purpletree Custom router Controller Router
 */
class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Purpletree\Marketplace\Helper\Data $dataHelper
    ) {
        $this->_actionFactory   = $actionFactory;
        $this->storeDetails     = $storeDetails;
        $this->_dataHelper      = $dataHelper;
    }
 
    /**
     * Validate and Match
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $manager = \Magento\Framework\App\ObjectManager::getInstance();
            $obj = $manager->create('Purpletree\Marketplace\Helper\Processdata');
        if ($request->getFullActionName() === 'marketplace_index_storeview' || $request->getFullActionName() === 'marketplace_index_stores') {
            return;
        }
        $prodata = $obj->getProcessingdata();
        /*
         * We will search “examplerouter” and “exampletocms” words and make forward depend on word
         * -examplerouter will forward to base router to match marketplace front name, 
		 test controller path and test controller class
         * -exampletocms will set front name to cms, controller path to page and action to view
         */
        if ($prodata) {
            $identifier = trim($request->getPathInfo(), '/');
            if ($identifier == $this->_dataHelper->getGeneralConfig('manage_links/sellers_link_seo')) {
                if ($this->_dataHelper->getGeneralConfig('manage_links/sellers_link_seo') != '') {
                    $request->setRouteName('marketplace')
                    ->setModuleName('marketplace')
                    ->setControllerName('index')
                    ->setActionName('stores');
                } else {
                    return;
                }
            } else {
                $sellerId = $this->storeDetails->storeIdByUrl($identifier);
                $isseller = $this->storeDetails->isSeller($sellerId);
                if ($isseller) {
                    /*
				* We must set module, controller path and action name + we will set page id 5 witch is about us page on
				* default magento 2 installation with sample data.
				*/
                        $request->setRouteName('marketplace')
                        ->setModuleName('marketplace')
                        ->setControllerName('index')
                        ->setActionName('storeview')
                        ->setParam('store', $identifier);
                } else {
                    //There is no match
                    return;
                }
            }
        } else {
              return;
        }
        /*
         * We have match and now we will forward action
         */
        return $this->_actionFactory->create(
            'Magento\Framework\App\Action\Forward',
            ['request' => $request]
        );
    }
}
