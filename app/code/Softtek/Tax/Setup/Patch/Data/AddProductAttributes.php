<?php

namespace Softtek\Tax\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\PatchInterface;
use Magento\Eav\Model\Config;
use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\AttributeSetRepository;
use Magento\Framework\Exception\InputException;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Catalog\Model\Product\Attribute\Source\Boolean as SourceBoolean;
use Magento\Catalog\Model\Product\Attribute\Backend\Boolean as BackendBoolean;

class AddProductAttributes implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CategorySetupFactory $categorySetupFactory,
        Config                   $eavConfig
    ) {
        $this->moduleDataSetup        = $moduleDataSetup;
        $this->categorySetupFactory   = $categorySetupFactory;
        $this->eavConfig              = $eavConfig;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $categorySetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);

        try {

            if (!$this->isExistingProductAttribute('imported')) {
                $categorySetup->addAttribute(Product::ENTITY, 'imported',
                    [
                        'type'                    => 'int',
                        'frontend'                => '',
                        'label'                   => 'Imported',
                        'input'                   => 'boolean',
                        'backend'                 => BackendBoolean::class,
                        'source'                  => SourceBoolean::class,
                        'global'                  => ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible'                 => true,
                        'required'                => false,
                        'user_defined'            => true,
                        'default'                 => '',
                        'searchable'              => false,
                        'filterable'              => false,
                        'comparable'              => false,
                        'visible_on_front'        => true,
                        'unique'                  => false,
                        'is_used_in_grid'         => true

                    ]
                );
            }

            if (!$this->isExistingProductAttribute('ipi')) {
                $categorySetup->addAttribute(Product::ENTITY, 'ipi',
                    [
                        'type'                    => 'int',
                        'frontend'                => '',
                        'label'                   => 'IPI',
                        'input'                   => 'boolean',
                        'backend'                 => BackendBoolean::class,
                        'source'                  => SourceBoolean::class,
                        'global'                  => ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible'                 => true,
                        'required'                => false,
                        'user_defined'            => true,
                        'default'                 => '',
                        'searchable'              => false,
                        'filterable'              => false,
                        'comparable'              => false,
                        'visible_on_front'        => true,
                        'unique'                  => false,
                        'is_used_in_grid'         => true
                    ]
                );
            }

            $attributeSetId = $categorySetup->getDefaultAttributeSetId(Product::ENTITY);
            $categorySetup->addAttributeToGroup(
                Product::ENTITY,
                $attributeSetId,
                'Default',
                'imported',
                100
            );

            $categorySetup->addAttributeToGroup(
                Product::ENTITY,
                $attributeSetId,
                'Default',
                'ipi',
                101
            );

        } catch (\Exception $exception) {
            throw new InputException(__($exception->getMessage()));
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    private function isExistingProductAttribute(string $attr_code)
    {
        $attr = $this->eavConfig->getAttribute(Product::ENTITY, $attr_code);
        return ($attr && $attr->getId());
    }
}
