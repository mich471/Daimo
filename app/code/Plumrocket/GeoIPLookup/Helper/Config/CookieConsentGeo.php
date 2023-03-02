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
 * @package     Plumrocket_Csp
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\GeoIPLookup\Helper\Config;

use Plumrocket\Base\Helper\ConfigUtils;

/**
 * @since 1.2.2
 */
class CookieConsentGeo extends ConfigUtils
{
    const XML_PATH_GEO_TARGETING = 'pr_cookie/main_settings/geo_targeting';
    const XML_PATH_STATES_GEO_TARGETING = 'pr_cookie/main_settings/geoip_restriction_usa_ccpa';

    /**
     * Retrieve config values
     *
     * @param null $store
     * @param null $scope
     * @return array
     */
    public function getGeoTargeting($store = null, $scope = null): array
    {
        $geoTargetingConfig = (string) $this->getConfig(
            self::XML_PATH_GEO_TARGETING,
            $store,
            $scope
        );

        return array_filter($this->prepareMultiselectValue($geoTargetingConfig));
    }

    /**
     * Retrieve config values
     *
     * @param null $store
     * @param null $scope
     * @return array
     */
    public function getStatesGeoTargeting($store = null, $scope = null): array
    {
        $geoTargetingConfig = (string) $this->getConfig(
            self::XML_PATH_STATES_GEO_TARGETING,
            $store,
            $scope
        );

        return $this->prepareMultiselectValue($geoTargetingConfig);
    }
}
