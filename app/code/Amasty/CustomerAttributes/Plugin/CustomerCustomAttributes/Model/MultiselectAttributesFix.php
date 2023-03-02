<?php
declare(strict_types=1);

namespace Amasty\CustomerAttributes\Plugin\CustomerCustomAttributes\Model;

use Magento\CustomerCustomAttributes\Model\CustomerAddressCustomAttributesProcessor;
use Magento\Quote\Api\Data\AddressInterface;

class MultiselectAttributesFix
{
    /**
     * Problem with our multiple attributes and Magento Customer Custom Attributes processor
     * @see CustomerAddressCustomAttributesProcessor::execute()
     *
     * @param CustomerAddressCustomAttributesProcessor $subject
     * @param AddressInterface $addressInformation
     */
    public function beforeExecute(
        CustomerAddressCustomAttributesProcessor $subject,
        AddressInterface $addressInformation
    ) {
        $customerCustomAttributes = $addressInformation->getCustomAttributes();
        if ($customerCustomAttributes) {
            foreach ($customerCustomAttributes as $customAttribute) {
                $customAttributeValue = $customAttribute->getValue();
                if ($customAttributeValue && is_array($customAttributeValue)) {
                    if (!isset($customAttributeValue['value'])) {
                        $customAttribute->setValue(['value' => $customAttributeValue]);
                    }
                }
            }
        }
    }
}
