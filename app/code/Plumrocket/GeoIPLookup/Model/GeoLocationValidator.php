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
 * @package     Plumrocket_GeoIPLookup
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\GeoIPLookup\Model;

use Magento\Framework\Exception\LocalizedException;
use Plumrocket\GeoIPLookup\Api\GeoLocationValidatorInterface;
use Plumrocket\GeoIPLookup\Api\LocationsListInterface;

/**
 * @since 1.2.0
 */
class GeoLocationValidator implements GeoLocationValidatorInterface
{
    /**
     * @var \Plumrocket\GeoIPLookup\Model\GeoIPLookup
     */
    private $geoIPLookup;

    /**
     * @var \Plumrocket\GeoIPLookup\Api\LocationsListInterface
     */
    private $locationsList;

    /**
     * ValidateLocation constructor.
     *
     * @param \Plumrocket\GeoIPLookup\Model\GeoIPLookup          $geoIPLookup
     * @param \Plumrocket\GeoIPLookup\Api\LocationsListInterface $locationsList
     */
    public function __construct(GeoIPLookup $geoIPLookup, LocationsListInterface $locationsList)
    {
        $this->geoIPLookup = $geoIPLookup;
        $this->locationsList = $locationsList;
    }

    /**
     * @inheritDoc
     */
    public function validate(array $regions, array $counties, array $usaStates = [], string $ip = null): bool
    {
        if (! $this->geoIPLookup->canUse()) {
            throw new LocalizedException(
                __('Plumrocket GeoIP Lookup is disabled or no one GeoIP databases is enabled')
            );
        }

        if ((empty($regions) && empty($counties))
            || in_array(LocationsListInterface::ALL, $regions, true)
        ) {
            return true;
        }

        if (in_array(LocationsListInterface::EU, $regions, true) && $this->geoIPLookup->isInEuropeanUnion()) {
            return true;
        }

        $countryCode = $this->geoIPLookup->getCountryCode();
        if (null !== $countryCode) {
            if ($usaStates && LocationsListInterface::COUNTRY_CODE_USA === $countryCode) {
                return in_array($this->geoIPLookup->getCurrentCountryState(), $usaStates, true);
            }

            return in_array($countryCode, $counties, true);
        }

        if (in_array(LocationsListInterface::UNKNOWN, $regions, true)) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function validateByMergedOptions(array $options, array $usaStates = [], string $ip = null): bool
    {
        $allRegionsKey = array_column($this->locationsList->getRegions(), 'value');

        $regions = [];
        $counties = [];
        foreach ($options as $option) {
            if (in_array($option, $allRegionsKey, true)) {
                $regions[] = $option;
            } else {
                $counties[] = $option;
            }
        }

        return $this->validate($regions, $counties, $usaStates, $ip);
    }
}
