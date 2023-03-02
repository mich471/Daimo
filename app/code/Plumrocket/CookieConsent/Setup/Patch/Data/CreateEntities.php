<?php
/**
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Setup\Patch\Data;

use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Plumrocket\CookieConsent\Setup\Patch\Data\Entities\GetCategoryEntities;
use Plumrocket\CookieConsent\Setup\Patch\Data\Entities\GetCookieEntities;

/**
 * @since 1.3.0
 */
class CreateEntities implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var \Plumrocket\CookieConsent\Setup\Patch\Data\Entities\GetCategoryEntities
     */
    private $getCategoryEntities;

    /**
     * @var \Plumrocket\CookieConsent\Setup\Patch\Data\Entities\GetCookieEntities
     */
    private $getCookieEntities;

    /**
     * @param \Magento\Eav\Model\Config                                     $eavConfig
     * @param \Magento\Eav\Setup\EavSetupFactory                            $eavSetupFactory
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface             $moduleDataSetup
     * @param \Plumrocket\CookieConsent\Setup\Patch\Data\Entities\GetCategoryEntities $getCategoryEntities
     * @param \Plumrocket\CookieConsent\Setup\Patch\Data\Entities\GetCookieEntities   $getCookieEntities
     */
    public function __construct(
        EavConfig $eavConfig,
        EavSetupFactory $eavSetupFactory,
        ModuleDataSetupInterface $moduleDataSetup,
        GetCategoryEntities $getCategoryEntities,
        GetCookieEntities $getCookieEntities
    ) {
        $this->eavConfig = $eavConfig;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->getCategoryEntities = $getCategoryEntities;
        $this->getCookieEntities = $getCookieEntities;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->installEntities($this->getCategoryEntities->execute());
        $eavSetup->installEntities($this->getCookieEntities->execute());
        $this->eavConfig->clear();

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritdoc
     */
    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $this->removeEntities($eavSetup, $this->getCategoryEntities->execute());
        $this->removeEntities($eavSetup, $this->getCookieEntities->execute());

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Remove entities attributes and types
     *
     * @param \Magento\Eav\Setup\EavSetup $eavSetup
     * @param array $entities
     * @return void
     */
    private function removeEntities(EavSetup $eavSetup, array $entities): void
    {
        foreach ($entities as $entityType => $entityData) {
            // remove attributes
            if (is_array($entityData['attributes']) && !empty($entityData['attributes'])) {
                foreach ($entityData['attributes'] as $attrCode => $attr) {
                    $eavSetup->removeAttribute($entityType, $attrCode);
                }
            }
            //remove Eav Entity Type
            $eavSetup->removeEntityType($entityType);
        }
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
