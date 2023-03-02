<?php

namespace Amasty\CustomerAttributes\Plugin\Magento\Customer\Model\Metadata;

class Form
{
    public function afterGetUserAttributes($subject, $result)
    {
        foreach ($result as &$attribute) {
            if ($attribute->getFrontendInput() == 'multiselectimg'
                || $attribute->getFrontendInput() == 'selectimg'
            ) {
                $frontendInput = substr($attribute->getFrontendInput(), 0, -3);
                $attribute->setFrontendInput($frontendInput);
           }
        }

        return $result;
    }
}
