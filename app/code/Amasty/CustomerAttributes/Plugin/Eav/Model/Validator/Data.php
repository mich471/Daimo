<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (http://www.amasty.com)
 * @package Amasty_CustomerAttributes
 */

namespace Amasty\CustomerAttributes\Plugin\Eav\Model\Validator;

use Amasty\CustomerAttributes\Model\Attribute;

class Data
{
    /**
     * @var \Magento\Customer\Model\AttributeMetadataDataProvider
     */
    protected $attributeMetadataDataProvider;

    public function __construct(
        \Magento\Customer\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider
    ) {
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
    }

    /**
     * @param \Magento\Eav\Model\Validator\Attribute\Data $subject
     * @param $entity
     */
    public function beforeIsValid(
        \Magento\Eav\Model\Validator\Attribute\Data $subject,
        $entity
    ) {
        $blacklist = [];
        $attributes = $this->attributeMetadataDataProvider->loadAttributesCollection(
            'customer',
            Attribute::AMASTY_ATTRIBUTE_CODE
        );

        foreach ($attributes as $attribute) {
            $blacklist[] = $attribute->getAttributeCode();
        }

        //magento 2.4 method renamed setAttributesBlackList to setDeniedAttributesList
        if (method_exists($subject, 'setDeniedAttributesList')) {
            $subject->setDeniedAttributesList($blacklist);
        } else {
            $subject->setAttributesBlackList($blacklist);
        }
    }
}
