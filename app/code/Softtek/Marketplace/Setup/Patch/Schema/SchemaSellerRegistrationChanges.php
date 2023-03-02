<?php
namespace Softtek\Marketplace\Setup\Patch\Schema;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class SchemaSellerRegistrationChanges
 * @package Softtek\Customer\Setup\Patch\Data
 */
class SchemaSellerRegistrationChanges implements SchemaPatchInterface
{
    /** @var ModuleDataSetupInterface */
    protected $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $setup = $this->moduleDataSetup;
        $setup->startSetup();

        $setup->getConnection()
            ->changeColumn(
                $setup->getTable('purpletree_marketplace_stores'),
                'entity_idpts',
                'entity_idpts', [
                    'type' => Table::TYPE_INTEGER,
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                    'comment' => 'Entity ID'
                ]
            );
        $setup->getConnection()
            ->changeColumn(
                $setup->getTable('purpletree_marketplace_stores'),
                'store_name',
                'store_name', [
                    'type' => Table::TYPE_TEXT,
                    'length' => 100,
                    'nullable' => true,
                    'comment' => 'Store Name'
                ]
            );
        $setup->getConnection()
            ->changeColumn(
                $setup->getTable('purpletree_marketplace_stores'),
                'store_url',
                'store_url', [
                    'type' => Table::TYPE_TEXT,
                    'length' => 30,
                    'nullable' => true,
                    'comment' => 'Store URL'
                ]
            );

        $setup->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
