<?php
namespace Magepow\CancelOrder\Setup\Patch\Schema;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class AddAuthorizeCancelFields
 * @package Softtek\Customer\Setup\Patch
 */
class AddAuthorizeCancelFields implements SchemaPatchInterface
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

        //Adding new fields/columns for seller CyberSource accounts and about Transaction Search API (TSA) to get info of 'Boleto' existing transactions
        $columns = [
            'databank_actype' => [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Bank Account Type'
            ],
            'seller_apply' => [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Seller - Apply'
            ],
            'seller_reason' => [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Seller - Reason'
            ],
            'seller_comment' => [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Seller - Comment'
            ]
        ];
        foreach ($columns as $name => $definition) {
            $this->moduleDataSetup->getConnection()->addColumn($this->moduleDataSetup->getTable('magepow_cancelrequest'), $name, $definition);
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
