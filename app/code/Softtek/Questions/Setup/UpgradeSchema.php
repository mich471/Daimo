<?php
/**
 * Softtek Questions Module
 *
 * @category    Softtek
 * @package     Questions
 * @author      J. Abraham Serena <jorge.serena@softtek.com>
 * @copyright   © Softtek 2022. All rights reserved.
 */
namespace Softtek\Questions\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.2', '<')) {

            $table = $installer->getTable('phpcuong_product_question');

            $columns = [
                'product_name' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Product Name'
                ],
                'product_idseller' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Id Seller Product'
                ],
                'product_url' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'URL product'
                ],
            ];

            $connection = $installer->getConnection();

            foreach ($columns as $name => $definition) {
                $connection->addColumn($table, $name, $definition);
            }
        }

        $installer->endSetup();
    }

}
