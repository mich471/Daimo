<?php
namespace Softtek\Marketplace\Setup\Patch\Schema;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class AddOrderStatusFields
 * @package Softtek_Marketplace
 */
class AddOrderStatusFields implements SchemaPatchInterface
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
        $this->moduleDataSetup->startSetup();

        //Adding new fields/columns for order status history
        $columns = [
            'sm_is_message' => [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => true,
                'default' => 0,
                'comment' => 'Is message of communication with the customer'
            ],
            'sm_seller_message' => [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => true,
                'default' => 0,
                'comment' => 'Is seller message'
            ],
            'sm_customer_message' => [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => true,
                'default' => 0,
                'comment' => 'Is customer message'
            ]
        ];
        foreach ($columns as $name => $definition) {
            $this->moduleDataSetup->getConnection()->addColumn($this->moduleDataSetup->getTable('sales_order_status_history'), $name, $definition);
        }

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
