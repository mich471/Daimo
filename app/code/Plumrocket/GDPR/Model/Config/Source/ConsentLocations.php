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

class ConsentLocations implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * GDPR Consent Locations
     */
    const CHECKOUT = 'checkout';
    const REGISTRATION = 'registration';
    const NEWSLETTER = 'newsletter';
    const CONTACT_US = 'contact_us';
    const POPUP_NOTIFY = 'popup_notify';
    const COOKIE = 'cookie';
    const CUSTOM = 'prgdpr_custom';
    const MY_ACCOUNT = 'my_account';

    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\Consent\Location\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ConsentLocationsGrouped
     */
    private $consentLocationsGroupedSource;

    /**
     * ConsentLocations constructor.
     *
     * @param \Plumrocket\GDPR\Model\ResourceModel\Consent\Location\CollectionFactory $collectionFactory
     * @param ConsentLocationsGrouped                                                 $consentLocationsGroupedSource
     */
    public function __construct(
        \Plumrocket\GDPR\Model\ResourceModel\Consent\Location\CollectionFactory $collectionFactory,
        \Plumrocket\GDPR\Model\Config\Source\ConsentLocationsGrouped $consentLocationsGroupedSource
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->consentLocationsGroupedSource = $consentLocationsGroupedSource;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        /** @var \Plumrocket\GDPR\Model\ResourceModel\Consent\Location\Collection $collection */
        $collection = $this->collectionFactory->create();

        return $collection->toOptionIdArray();
    }

    /**
     * @deprecated since 1.4.0 - use ConsentLocationsGrouped model instead
     *
     * @return array
     */
    public function toGroupedOptionAssocArray()
    {
        return $this->consentLocationsGroupedSource->toOptionArray();
    }
}
