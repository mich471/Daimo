<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Setup;

use Plumrocket\GDPR\Model\Config\Source\ConsentLocations as LocationsSource;

class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    /**
     * @var \Plumrocket\GDPR\Setup\CheckboxSetupFactory
     */
    private $checkboxSetupFactory;

    /**
     * @var \Plumrocket\DataPrivacyApi\Api\CheckboxRepositoryInterface
     */
    private $checkboxRepository;

    /**
     * @var \Plumrocket\DataPrivacyApi\Api\CheckboxProviderInterface
     */
    private $checkboxProvider;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @param \Plumrocket\GDPR\Setup\CheckboxSetupFactory                $checkboxSetupFactory
     * @param \Plumrocket\DataPrivacyApi\Api\CheckboxRepositoryInterface $checkboxRepository
     * @param \Magento\Framework\App\State                               $state
     * @param \Plumrocket\DataPrivacyApi\Api\CheckboxProviderInterface   $checkboxProvider
     * @param \Magento\Eav\Model\Config                                  $eavConfig
     */
    public function __construct(
        \Plumrocket\GDPR\Setup\CheckboxSetupFactory $checkboxSetupFactory,
        \Plumrocket\DataPrivacyApi\Api\CheckboxRepositoryInterface $checkboxRepository,
        \Magento\Framework\App\State $state,
        \Plumrocket\DataPrivacyApi\Api\CheckboxProviderInterface $checkboxProvider,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->checkboxSetupFactory = $checkboxSetupFactory;
        $this->checkboxRepository = $checkboxRepository;

        try {
            $state->setAreaCode('adminhtml');
        } catch (\Exception $e) { // phpcs:ignore -- fix for specific client cases
            // do nothing
        }
        $this->checkboxProvider = $checkboxProvider;
        $this->eavConfig = $eavConfig;
    }

    /**
     * Upgrades data for a module
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function upgrade(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();

        /** @var CheckboxSetup $checkboxSetup */
        $checkboxSetup = $this->checkboxSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.4.0', '<')) {
            $checkboxSetup->installEntities();
        }

        if (version_compare($context->getVersion(), '1.4.4', '<')) {
            $checkboxSetup->addAttribute(
                \Plumrocket\GDPR\Model\Checkbox::ENTITY,
                'geo_targeting_usa_states',
                [
                    'type' => 'text',
                    'label' => 'U.S. states',
                    'required' => false,
                    'default' => 'all',
                    'input' => 'multiselect',
                    'source' => \Plumrocket\GDPR\Model\Checkbox\Attribute\Source\GeoTargetingStates::class,
                    'backend' => \Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend::class,
                    'sort_order' => 650,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'General Information',
                    'note' => 'You can select one or multiple U.S. states. For example,
                        select "California" for CCPA law.'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.4.5', '<')) {
            $checkboxSetup->updateAttribute(
                \Plumrocket\GDPR\Model\Checkbox::ENTITY,
                'location_key',
                ['frontend_input' => 'multiselect']
            );
        }

        if (version_compare($context->getVersion(), '1.7.0', '<')) {
            // if we did't clean cache magento would throw exception "Invalid entity_type specified"
            $this->eavConfig->clear();
            $checkboxes = $this->checkboxProvider->getAll();

            foreach ($checkboxes as $checkbox) {
                $locationKeys = $checkbox->getLocationKeys();
                $locationKeys[] = LocationsSource::MY_ACCOUNT;
                $checkbox->setLocationKeys($locationKeys);
                $this->checkboxRepository->save($checkbox);
            }
        }

        $setup->endSetup();
    }
}
