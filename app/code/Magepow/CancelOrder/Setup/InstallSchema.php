<?php
namespace Magepow\CancelOrder\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();
        $table_magepow_cancelrequest = $setup->getConnection()->newTable($setup->getTable('magepow_cancelrequest'));

        $table_magepow_cancelrequest->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'nullable' => false,
                'primary' => true,
                'unsigned' => true,
            ],
            'Entity ID'
        );

        $table_magepow_cancelrequest->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Order ID'
        );

        $table_magepow_cancelrequest->addColumn(
            'payment_method',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Payment Method'
        );

        $table_magepow_cancelrequest->addColumn(
            'cc_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Credit Card Type'
        );

        $table_magepow_cancelrequest->addColumn(
            'cc_last_4',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Last 4 digits of credit card '
        );

        $table_magepow_cancelrequest->addColumn(
            'grand_total',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Grand Total'
        );

        $table_magepow_cancelrequest->addColumn(
            'reason',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Cancellation reason'
        );

        $table_magepow_cancelrequest->addColumn(
            'comment_reason',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Cancellation reason comment'
        );

        $table_magepow_cancelrequest->addColumn(
            'databank_cnpj',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'CNPJ'
        );

        $table_magepow_cancelrequest->addColumn(
            'databank_banknumber',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Bank number'
        );

        $table_magepow_cancelrequest->addColumn(
            'databank_agnumber',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Agency bank number'
        );

        $table_magepow_cancelrequest->addColumn(
            'databank_acnumber',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Account bank number'
        );

        $table_magepow_cancelrequest->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            null,
            [],
            'Status'
        );

        $setup->getConnection()->createTable($table_magepow_cancelrequest);
        $setup->endSetup();
    }
}
