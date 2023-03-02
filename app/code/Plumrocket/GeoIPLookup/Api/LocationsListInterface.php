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

namespace Plumrocket\GeoIPLookup\Api;

/**
 * @since 1.2.0
 */
interface LocationsListInterface
{
    /**
     * All site visitors
     */
    const ALL = 'all';

    /**
     * Only visitors from EU countries
     */
    const EU = 'eu';

    /**
     * Only visitors from unrecognized locations
     */
    const UNKNOWN = 'unknown';

    const COUNTRY_CODE_USA = 'US';

    const USA_STATE_CALIFORNIA = 'California';

    /**
     * Return array of regions as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function getRegions(): array;

    /**
     * Return array of counties as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function getCountries(): array;

    /**
     * Return array of states as value-label pairs
     *
     * @param string $countryName
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function getCountryStates(string $countryName): array;
}
