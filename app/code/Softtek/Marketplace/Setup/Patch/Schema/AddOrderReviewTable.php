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
class AddOrderReviewTable implements SchemaPatchInterface
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
        $table = $this->moduleDataSetup->getConnection()
            ->newTable($tableName)
            ->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Order ID'
            )
            ->addColumn(
                'question_1_raking',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Classificação da pergunta 1'
            )
            ->addColumn(
                'question_2_raking',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Question 2 Raking'
            )
            ->addColumn(
                'question_3_raking',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Question 3 Raking'
            )
            ->addColumn(
                'comment',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'Comment'
            )
            ->addColumn(
                'approved',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => 0
                ],
                'Approved'
            );
        $this->moduleDataSetup->getConnection()->createTable($table);

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
