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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Plumrocket\DataPrivacyApi\Api\ConsentLocationTypeInterface as ConsentLocationType;
use Plumrocket\DataPrivacyApi\Api\Data\ConsentLocationInterface as ConsentLocation;

/**
 * @see \Plumrocket\DataPrivacy\Setup\RecurringData
 * @deprecated since 3.1.0
 */
class RecurringData implements \Magento\Framework\Setup\InstallDataInterface
{
    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\Consent\Location
     */
    private $consentLocationResource;

    /**
     * @var \Plumrocket\GDPR\Model\Consent\LocationFactory
     */
    private $consentLocationFactory;

    /**
     * @var \Plumrocket\GDPR\Api\ConsentLocationRegistryInterface
     */
    private $consentLocationRegistry;

    /**
     * RecurringData constructor.
     *
     * @param \Plumrocket\GDPR\Model\ResourceModel\Consent\Location     $consentLocationResource
     * @param \Plumrocket\GDPR\Api\Data\ConsentLocationInterfaceFactory $consentLocationFactory
     * @param \Plumrocket\GDPR\Api\ConsentLocationRegistryInterface     $consentLocationRegistry
     */
    public function __construct(
        \Plumrocket\GDPR\Model\ResourceModel\Consent\Location $consentLocationResource,
        \Plumrocket\GDPR\Api\Data\ConsentLocationInterfaceFactory $consentLocationFactory,
        \Plumrocket\GDPR\Api\ConsentLocationRegistryInterface $consentLocationRegistry
    ) {
        $this->consentLocationResource = $consentLocationResource;
        $this->consentLocationFactory = $consentLocationFactory;
        $this->consentLocationRegistry = $consentLocationRegistry;
    }

    /**
     * @inheritDoc
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installedLocationKeys = $this->consentLocationResource->getAllLocationKeys();

        $locations = $this->consentLocationRegistry->getLocations();

        if ($locations) {
            foreach ($locations as $locationKey => $locationData) {
                if (! in_array($locationKey, $installedLocationKeys, true)) {
                    if (! $locationKey || ! isset($locationData[ConsentLocation::NAME])) {
                        continue;
                    }

                    /** @var ConsentLocation $location */
                    $location = $this->consentLocationFactory->create();

                    $location->setLocationKey((string)$locationKey);
                    $location->setName((string)$locationData[ConsentLocation::NAME]);
                    $location->setDescription((string)($locationData[ConsentLocation::DESCRIPTION] ?? ''));

                    $location->setType(
                        (int)($locationData[ConsentLocation::TYPE] ?? ConsentLocationType::TYPE_CUSTOM)
                    );
                    $location->setVisibility((int)($locationData[ConsentLocation::VISIBLE] ?? 1));

                    $this->consentLocationResource->save($location);
                }
            }
        }
    }
}
