<?php
declare(strict_types=1);

namespace Amasty\CustomerAttributes\Model\Checkout;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Customer\Model\Attribute;
use Magento\Customer\Model\ResourceModel\Attribute\Collection;
use Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Eav\Api\Data\AttributeOptionInterface;

class AttributesConfigProvider implements ConfigProviderInterface
{
    const CONFIG_KEY = 'amastyCustomerAttributeOptionsConfig';

    /**
     * @var CollectionFactory
     */
    private $customerAttributeCollectionFactory;

    public function __construct(
        CollectionFactory $customerAttributeCollectionFactory
    ) {
        $this->customerAttributeCollectionFactory = $customerAttributeCollectionFactory;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        $config = [];
        /** @var Collection $collection */
        $collection = $this->customerAttributeCollectionFactory->create();
        $collection
            ->addVisibleFilter()
            ->addFieldToFilter(AttributeInterface::IS_USER_DEFINED, ['eq' => 1]);

        /** @var Attribute $attribute */
        foreach ($collection->getItems() as $attribute) {
            $options = (array)$attribute->getOptions();

            if (!empty($options)) {
                $config[$attribute->getAttributeCode()] = $this->getPrepareOptions($options);
            }
        }

        return [self::CONFIG_KEY => $config];
    }

    /**
     * @param array $options
     * @return array
     */
    private function getPrepareOptions(array $options): array
    {
        $prepared = [];

        /** @var AttributeOptionInterface $option */
        foreach ($options as $option) {
            if ($option->getValue() || $option->getLabel()) {
                $prepared[$option->getValue()] = $option->getLabel();
            }
        }

        return $prepared;
    }
}
