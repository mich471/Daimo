<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Softtek\Vendor\Setup\Patch\Data;

use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Model\Entity\Attribute\Set;
use Softtek\Vendor\Model\Customer\Attribute\Source\OperatingSegmentType;

/**
* Patch is mechanism, that allows to do atomic upgrade data changes
*/
class OperatingSegment implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory,
        ModuleDataSetupInterface $moduleDataSetup
    )
    {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $attributeCode = 'operating_segment';
        $entityType = 'customer';

        $customerEntity = $customerSetup->getEavConfig()->getEntityType($entityType);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet Set */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(
            $entityType,
            $attributeCode,
            [
                'type' => 'int',
                'label' => 'Segmento de atuação',
                'input' => 'select',
                'source' => OperatingSegmentType::class,
                'required' => false,
                'visible' => true,
                'sort_order' => 22,
                'user_defined'  =>  true,
                'system' => false,
                'default_value' => '',
                'position' => 22
            ]
        );

        $attribute = $customerSetup->getEavConfig()->getAttribute($entityType, $attributeCode)
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => [
                    'adminhtml_checkout',
                    'adminhtml_customer',
                    'customer_account_create',
                    'customer_account_edit'
                ],
            ]);
        $attribute->save();
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    public static function getVersion()
    {
        return '1.0.5';
    }
}
