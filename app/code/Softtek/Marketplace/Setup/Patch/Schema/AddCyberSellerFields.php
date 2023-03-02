<?php
namespace Softtek\Marketplace\Setup\Patch\Schema;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;

/**
 * Class AddCyberSellerFields
 * @package Softtek\Customer\Setup\Patch
 */
class AddCyberSellerFields implements SchemaPatchInterface
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

        //Adding new fields/columns for seller CyberSource accounts
        $columns = [
            'cs_cc_rest_api_key' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Cybersource CC REST API Key'
            ],
            'cs_cc_rest_api_secret_key' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Cybersource CC REST API Secret Key'
            ],
            'cs_cc_merchant_id' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Cybersource CC Merchant ID'
            ],
            'cs_cc_org_id' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Cybersource CC Org ID'
            ],
            'cs_cc_profile_id' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Cybersource CC Profile ID'
            ],
            'cs_cc_key_alias' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Cybersource CC Key Alias'
            ],
            'cs_cc_key_pass' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Cybersource CC Key Pass'
            ],
            'cs_cc_key_filename' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Cybersource CC Key FileName'
            ],
            'cs_pt_rest_api_key' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Cybersource PT REST API Key'
            ],
            'cs_pt_merchant_id' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Cybersource PT Merchant ID'
            ],
            'cs_pt_same_as_cc' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => true,
                'comment' => 'Cybersource Purchase Ticket same as Credit Card'
            ],
            'cs_environment' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => true,
                'comment' => 'Cybersource Environment'
            ]
        ];
        foreach ($columns as $name => $definition) {
            $this->moduleDataSetup->getConnection()->addColumn($this->moduleDataSetup->getTable('purpletree_marketplace_stores'), $name, $definition);
        }

        //Removing unnecessary fields/columns
        $columns = ['store_cyber_user_id', 'store_cyber_terminal_id', 'store_cyber_merchant_id'];
        foreach ($columns as $column) {
            $this->moduleDataSetup->getConnection()->dropColumn(
                $this->moduleDataSetup->getTable('purpletree_marketplace_stores'),
                $column
            );
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
