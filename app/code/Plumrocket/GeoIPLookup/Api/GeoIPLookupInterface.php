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

interface GeoIPLookupInterface
{
    /**
     * Returns GeoIpData with possibility to choose services and their priority
     *
     * @param string $ip
     * @param string $params comma separated list of services to use, e.g. 'ipapi,maxmind,iptocountry'
     * @api
     * @return string|array
     */
    public function getGeoIpData($ip, $params = '');

    /**
     * Returns GeoIpData
     *
     * @param string $ip
     * @api
     * @return string|array
     */
    public function getGeoIpDataWithDefaultServices($ip);
}
