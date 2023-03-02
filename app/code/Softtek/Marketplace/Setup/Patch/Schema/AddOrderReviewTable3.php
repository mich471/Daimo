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
class AddOrderReviewTable3 implements SchemaPatchInterface
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
            ->addColumn($tableName, 'created_at', [
                'type' => Table::TYPE_TIMESTAMP,
                'nullable' => true,
                'default' => Table::TIMESTAMP_INIT,
                'comment' => 'Created At'
            ]);

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
