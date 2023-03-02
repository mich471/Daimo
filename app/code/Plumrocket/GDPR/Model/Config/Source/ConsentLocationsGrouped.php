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
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource as EavAbstractSource;

class ConsentLocationsGrouped extends EavAbstractSource
{
    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\Consent\Location\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Plumrocket\GDPR\Api\ConsentLocationTypeInterface
     */
    private $consentLocationType;

    /**
     * ConsentLocations constructor.
     *
     * @param \Plumrocket\GDPR\Model\ResourceModel\Consent\Location\CollectionFactory $collectionFactory
     * @param \Plumrocket\GDPR\Api\ConsentLocationTypeInterface                       $consentLocationType
     */
    public function __construct(
        \Plumrocket\GDPR\Model\ResourceModel\Consent\Location\CollectionFactory $collectionFactory,
        \Plumrocket\GDPR\Api\ConsentLocationTypeInterface $consentLocationType
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->consentLocationType = $consentLocationType;
    }

    /**
     * @return array
     */
    public function toOptionArray() : array
    {
        /** @var \Plumrocket\GDPR\Model\ResourceModel\Consent\Location\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addVisibleFilter();

        $noneOptionPrototype = [
            'value'  => '',
            'label'  => '[ none ]',
            'disabled' => 'disabled',
        ];

        $result = $this->consentLocationType->toOptionArray();

        foreach ($collection->getItems() as $item) {
            $result[$item->getType()]['value'][] = [
                'value' => $item->getLocationKey(),
                'label' => $item->getName(),
            ];
        }

        foreach ($result as $typeId => $options) {
            if (! $options['value']) {
                $result[$typeId]['value'][] = $noneOptionPrototype;
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getAllOptions() : array
    {
        return $this->toOptionArray();
    }
}
