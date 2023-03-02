<?php

namespace Amasty\CustomerAttributes\Setup;

use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Customer\Model\ResourceModel\Attribute\Collection;
use Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if ($context->getVersion() && version_compare($context->getVersion(), '1.1.1', '<')) {
            $this->removeUnusedAttribute();
        }
        if ($context->getVersion() && version_compare($context->getVersion(), '2.2.4', '<')) {
            $this->excludeAttributesFromFulltextIndex($setup);
        }

        $setup->endSetup();
    }

    /**
     * in version 1.0.0 was added attribute which now is not needed any more
     */
    private function removeUnusedAttribute()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, 'am_is_activated');
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    private function excludeAttributesFromFulltextIndex(ModuleDataSetupInterface $setup)
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $attributeIds = $collection
            ->addFieldToFilter(AttributeInterface::IS_USER_DEFINED, ['eq' => 1])
            ->addFieldToFilter(AttributeInterface::ATTRIBUTE_CODE, ['neq' => 'customer_activated'])
            ->getAllIds();

        if (!empty($attributeIds)) {
            $setup->getConnection()->update(
                $setup->getTable('customer_eav_attribute'),
                [AttributeMetadataInterface::IS_SEARCHABLE_IN_GRID => 0],
                'attribute_id IN(' . implode(',', $attributeIds) . ')'
            );
        }
    }
}
