<?php
namespace Softtek\Marketplace\Setup\Patch\Schema;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class AddOrderReviewTable
 * @package Softtek_Marketplace
 */
class AddOrderReviewTable2 implements SchemaPatchInterface
{
    /** @var ModuleDataSetupInterface */
    protected $moduleDataSetup;

    /** @var SchemaSetupInterface */
    protected $schemaSetupInterface;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        SchemaSetupInterface $schemaSetupInterface
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->schemaSetupInterface = $schemaSetupInterface;
    }
    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $tableName = $this->moduleDataSetup->getTable('st_order_review');
        $this->moduleDataSetup->getConnection()
            ->changeColumn($tableName, 'order_id', 'order_id', [
                    'type' => Table::TYPE_INTEGER,
                    'identity' => false,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => false,
                    'comment' => 'Order ID'
                ]
        );
        $this->moduleDataSetup->getConnection()
            ->dropForeignKey($tableName, $this->schemaSetupInterface->getFkName(
                'st_order_review',
                'order_id',
                'main_table',
                'entity_id'
            ));
        $this->moduleDataSetup->run("ALTER TABLE {$tableName} DROP PRIMARY KEY;");
        $this->moduleDataSetup->getConnection()
            ->addColumn($tableName, 'review_id', [
                'type' => Table::TYPE_INTEGER,
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
                'comment' => 'Review ID'
            ]);
        $this->moduleDataSetup->getConnection()->addForeignKey(
            $this->schemaSetupInterface->getFkName(
                'st_order_review',
                'order_id',
                'main_table',
                'entity_id'
            ),
            $this->moduleDataSetup->getTable('st_order_review'),
            'order_id',
            $this->moduleDataSetup->getTable('sales_order'),
            'entity_id'
        );

        $this->moduleDataSetup->endSetup();
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
