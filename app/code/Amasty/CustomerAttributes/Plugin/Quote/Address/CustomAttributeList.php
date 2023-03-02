<?php

namespace Amasty\CustomerAttributes\Plugin\Quote\Address;

use Magento\Customer\Model\AttributeMetadataDataProvider;
use Magento\CustomerCustomAttributes\Model\Quote\Address\CustomAttributeList as CustomerCustomAttributesList;
use Magento\Quote\Model\Quote\Address\CustomAttributeList as QuoteCustomAttributeList;

class CustomAttributeList
{
    /**
     * @var AttributeMetadataDataProvider
     */
    protected $attributeMetadataDataProvider;

    /**
     * @var array
     */
    protected $attributes = [];

    public function __construct(
        AttributeMetadataDataProvider $attributeMetadataDataProvider
    ) {
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
    }

    /**
     * @param QuoteCustomAttributeList|CustomerCustomAttributesList $subject
     * @param array $result
     * @return array
     */
    public function afterGetAttributes($subject, $result)
    {
        if (!$this->attributes) {
            /** @var \Magento\Eav\Api\Data\AttributeInterface[] $attributes */
            $attributes = $this->attributeMetadataDataProvider->loadAttributesCollection(
                'customer',
                'customer_attributes_checkout'
            );
            $elements = [];
            foreach ($attributes as $attribute) {
                $elements[$attribute->getAttributeCode()] = $attribute;
            }

            $this->attributes = array_merge($result, $elements);
        }

        return $this->attributes;
    }
}
