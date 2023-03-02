<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\DataPrivacy\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Plumrocket\DataPrivacy\Model\Consent\Location\ConfigProvider;
use Plumrocket\DataPrivacy\Model\ResourceModel\Consent\Location;
use Plumrocket\DataPrivacyApi\Api\Data\ConsentLocationInterface as ConsentLocation;
use Plumrocket\DataPrivacyApi\Api\Data\ConsentLocationInterfaceFactory;

/**
 * @since 3.1.0
 */
class RecurringData implements InstallDataInterface
{

    /**
     * @var \Plumrocket\DataPrivacy\Model\ResourceModel\Consent\Location
     */
    private $consentLocationResource;

    /**
     * @var \Plumrocket\DataPrivacyApi\Api\Data\ConsentLocationInterfaceFactory
     */
    private $consentLocationFactory;

    /**
     * @var \Plumrocket\DataPrivacy\Model\Consent\Location\ConfigProvider
     */
    private $locationConfigProvider;

    /**
     * @param \Plumrocket\DataPrivacy\Model\ResourceModel\Consent\Location        $consentLocationResource
     * @param \Plumrocket\DataPrivacyApi\Api\Data\ConsentLocationInterfaceFactory $consentLocationFactory
     * @param \Plumrocket\DataPrivacy\Model\Consent\Location\ConfigProvider       $locationConfigProvider
     */
    public function __construct(
        Location $consentLocationResource,
        ConsentLocationInterfaceFactory $consentLocationFactory,
        ConfigProvider $locationConfigProvider
    ) {
        $this->consentLocationResource = $consentLocationResource;
        $this->consentLocationFactory = $consentLocationFactory;
        $this->locationConfigProvider = $locationConfigProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installedLocationKeys = $this->consentLocationResource->getAllLocationKeys();

        $locations = $this->locationConfigProvider->get();

        if ($locations) {
            foreach ($locations as $locationKey => $locationData) {
                if (! in_array($locationKey, $installedLocationKeys, true)) {
                    if (! $locationKey || ! isset($locationData[ConsentLocation::NAME])) {
                        continue;
                    }

                    /** @var ConsentLocation $location */
                    $location = $this->consentLocationFactory->create();

                    $location->setLocationKey($locationKey);
                    $location->setName($locationData[ConsentLocation::NAME]);
                    $location->setDescription($locationData[ConsentLocation::DESCRIPTION]);
                    $location->setType($locationData[ConsentLocation::TYPE]);
                    $location->setVisibility($locationData[ConsentLocation::VISIBLE]);

                    $this->consentLocationResource->save($location);
                }
            }
        }
    }
}
