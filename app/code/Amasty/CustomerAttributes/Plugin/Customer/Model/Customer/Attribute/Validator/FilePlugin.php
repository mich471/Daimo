<?php

namespace Amasty\CustomerAttributes\Plugin\Customer\Model\Customer\Attribute\Validator;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Customer\Attribute\Validator\File;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Api\AttributeInterface;

class FilePlugin
{
    const FRONTEND_INPUT_FILE = 'amasty_cust_attr_file';

    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @var null|AbstractAttribute
     */
    private $attribute = null;

    public function __construct(EavConfig $eavConfig)
    {
        $this->eavConfig = $eavConfig;
    }

    /**
     * @param File $subject
     * @param AttributeInterface $customAttribute
     * @return AttributeInterface[]
     */
    public function beforeValidate(File $subject, AttributeInterface $customAttribute)
    {
        $this->attribute = $this->eavConfig->getAttribute(Customer::ENTITY, $customAttribute->getAttributeCode());
        if ($this->attribute->getFrontendInput() === 'file' && !$customAttribute->getValue()) {
            $this->attribute->setFrontendInput(self::FRONTEND_INPUT_FILE);
        }

        return [$customAttribute];
    }

    /**
     * @param File $subject
     * @param null $result
     * @param AttributeInterface $customAttribute
     * @return null
     */
    public function afterValidate(File $subject, $result, AttributeInterface $customAttribute)
    {
        if ($this->attribute && $this->attribute->getFrontendInput() === self::FRONTEND_INPUT_FILE) {
            $this->attribute->setFrontendInput('file');
        }

        return $result;
    }
}
