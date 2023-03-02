<?php

/**
 * Purpletree_Marketplace RemoveSeller
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

namespace Purpletree\Marketplace\Block\Adminhtml\Customer\Edit;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Customer account form block
 */
class RemoveSeller extends Generic implements TabInterface
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->customerRepository = $customerRepository;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $registry, $formFactory, $data);
    }
 
    /**
     * @return string|null
     */
    public function getIsSeller()
    {
        $customer = $this->customerRepository->getById($this->getCustomerId());
        if (!empty($customer->getCustomAttribute('is_seller'))) {
            return $customer->getCustomAttribute('is_seller')->getValue();
        } else {
            return 0;
        }
    }
    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }
 
    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Remove as Seller');
    }
 
    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Remove as Seller');
    }
 
    /**
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->getCustomerId()) {
            if ($this->getIsSeller() == 1) {
                return true;
            }
        }
        return false;
    }
 
    /**
     * @return bool
     */
    public function isHidden()
    {
        if ($this->getCustomerId()) {
            return false;
        }
        return true;
    }
 
    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }
 
    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return '';
    }
 
    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    public function initForm()
    {
        if (!$this->canShowTab()) {
            return $this;
        }
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('is_seller_');
         
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Become Seller')]);
         $fieldset->addField(
             'remove_seller',
             'checkbox',
             [
                'label' => __('Remove as Seller'),
                'name' => 'seller[remove_seller]',
                'data-form-part' => $this->getData('target_form'),
                'onchange' => 'this.value = this.checked;'
             ]
         );
        
        $this->setForm($form);
        return $this;
    }
    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->canShowTab()) {
            $this->initForm();
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
