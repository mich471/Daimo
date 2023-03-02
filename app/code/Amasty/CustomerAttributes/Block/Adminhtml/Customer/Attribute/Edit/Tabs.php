<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (http://www.amasty.com)
 * @package Amasty_CustomerAttributes
 */

namespace Amasty\CustomerAttributes\Block\Adminhtml\Customer\Attribute\Edit;

use Amasty\CustomerAttributes\Model\Attribute;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Tabs as BackendTabs;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;

class Tabs extends BackendTabs
{
    /**
     * @var Registry $registry
     */
    private $registry;

    /**
     * @var Attribute
     */
    private $attribute;

    public function __construct(
        Context $context,
        EncoderInterface $jsonEncoder,
        Session $authSession,
        Registry $registry,
        Attribute $attribute,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->attribute = $attribute;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('customer_attribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Attribute Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab(
            'main',
            [
                'label' => __('Properties'),
                'title' => __('Properties'),
                'content' => $this->getChildHtml('main'),
                'active' => true
            ]
        );
        $this->addTab(
            'labels',
            [
                'label' => __('Manage Label / Options'),
                'title' => __('Manage Label / Options'),
                'content' => $this->getChildHtml('options')
            ]
        );

        $frontendInput = ['multiselect', 'select', 'checkbox', 'boolean', 'multiselectimg', 'selectimg'];
        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attributeObject */
        $attributeObject = $this->registry->registry('entity_attribute');

        if (in_array($attributeObject->getFrontendInput(), $frontendInput)
            && $this->attribute->isOurAttribute($attributeObject->getAttributeCode())
        ) {
            $this->addTab(
                'reports',
                [
                    'label' => __('Reports'),
                    'title' => __('Reports'),
                    'content' => $this->getChildHtml('reports')
                ]
            );
        }

        return parent::_beforeToHtml();
    }
}
