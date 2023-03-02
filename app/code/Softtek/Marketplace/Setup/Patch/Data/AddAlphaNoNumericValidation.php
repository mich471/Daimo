<?php
namespace Softtek\Marketplace\Setup\Patch\Data;
use Magento\Eav\Setup\EavSetup;
use Magento\Customer\Model\Customer;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class AddAlphaNoNumericValidation
 * @package Softtek\Customer\Setup\Patch
 */
class AddAlphaNoNumericValidation implements DataPatchInterface
{
    /** @var ModuleDataSetupInterface */
    private $moduleDataSetup;
    /** @var EavSetupFactory */
    private $eavSetupFactory;
    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }
    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->updateAttribute(Customer::ENTITY, 'firstname', ['frontend_class' => 'alpha-no-numeric']);
        $eavSetup->updateAttribute(Customer::ENTITY, 'lastname', ['frontend_class' => 'alpha-no-numeric']);
        $eavSetup->updateAttribute(Customer::ENTITY, 'socialname', ['frontend_class' => '']);
        $eavSetup->updateAttribute(Customer::ENTITY, 'tradename', ['frontend_class' => '']);
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
