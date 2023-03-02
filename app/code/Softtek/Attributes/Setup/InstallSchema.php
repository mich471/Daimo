<?php
/**
 * Softtek Attributes Module
 *
 * @package Softtek_Attributes
 * @author Paul Soberanes <paul.soberanes@softtek.com>
 * @copyright Softtek 2020
 */
namespace Softtek\Attributes\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /* While module install, creates columns in inventory_source table */
        $eavTable1 = $installer->getTable('inventory_source');

        $columns = [
            'seller_name' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Seller name',
            ],
            'seller_id' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Seller id',
            ]
        ];

        $connection = $installer->getConnection();
        foreach ($columns as $name => $definition) {
            $connection->addColumn($eavTable1, $name, $definition);
        }

        $installer->endSetup();
    }
}
