<?php
namespace Softtek\Marketplace\Setup\Patch\Schema;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class AddCyberSellerFields
 * @package Softtek\Customer\Setup\Patch
 */
class AddCyberSellerFields2 implements SchemaPatchInterface
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
            'cs_tsa_pt_rest_api_key' => [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Cybersource TSA PT REST API Key'
            ],
            'cs_tsa_pt_rest_api_secret_key' => [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Cybersource TSA PT REST API Shared Secret Key'
            ]
        ];
        foreach ($columns as $name => $definition) {
            $this->moduleDataSetup->getConnection()->addColumn($this->moduleDataSetup->getTable('purpletree_marketplace_stores'), $name, $definition);
        }

        //Adding field to orders table to know when was the last API consult of status to CyberSource
        $this->moduleDataSetup->getConnection()
            ->addColumn($this->moduleDataSetup->getTable('sales_order'), 'st_cs_last_api_check', [
                'type' => Table::TYPE_TIMESTAMP,
                'nullable' => true,
                'default' => Table::TIMESTAMP_INIT,
                'comment' => 'Cybersource TSA - Last API Call date'
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
