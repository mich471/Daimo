<?php
/**
 * Softtek Marketplace Module
 *
 * @category    Softtek
 * @package     Softtek_Marketplace
 * @author      J. Abraham Serena <jorge.serena@softtek.com>
 * @copyright   © Softtek 2022. All rights reserved.
 */
namespace Softtek\Marketplace\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.0', '<')) {

            $table = $installer->getTable('purpletree_marketplace_stores');

            $columns = [
                'store_cyber_user_id' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Cybersource User ID'
                ],
                'store_cyber_terminal_id' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Cybersource Terminal ID'
                ],
                'store_cyber_merchant_id' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Cybersource Merchant ID'
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
