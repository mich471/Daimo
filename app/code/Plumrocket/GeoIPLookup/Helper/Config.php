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
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GeoIPLookup\Helper;

use Plumrocket\Base\Helper\AbstractConfig;
use Plumrocket\GeoIPLookup\Model\Base\Information;

/**
 * Class Config use for retrieve module configuration
 */
class Config extends AbstractConfig
{
    const SECTION_ID = Information::CONFIG_SECTION;

    /**
     * Name of Maxmind Group
     */
    const MAXMIND_GROUP = 'maxmindgeoip';

    /**
     * Name of IpToCountry Group
     */
    const IPTOCOUNTRY_GROUP = 'iptocountry';

    /**
     * Name of Ipapi Group
     */
    const IPAPI_GROUP = 'ipapigeoip';

    /**
     * Name of GeoIp Test Group
     */
    const GEOIPTEST_GROUP = 'geoiptest';

    /**
     * Local File Path
     */
    const LOCAL_PATH = 'prgeoiplookup/data/';

    /**
     * Name of GeoIp Test Group
     */
    const IPAPI_CONNECTION_URL = 'api.ipapi.com';

    /**
     * Default Ip for testing
     */
    const DEFAULT_IP = '192.168.1.1';

    /**
     * IpToCountry source file on Wiki
     */
    const IPTOCOUNTRY_SOURCE_FILE = 'https://plumrocket.com/docs/wp-content/uploads/geoip/IpToCountry.csv';

    /**
     * Version source file on Wiki
     */
    const PATH_VERSION_FILE  = 'https://plumrocket.com/docs/wp-content/uploads/geoip/versions.csv';

    /**
     * Maxmind source file on Wiki
     */
    const PATH_MAXMIND_BLOCKS = 'https://plumrocket.com/docs/wp-content/uploads/geoip/GeoLite2-City-Blocks-IPv4.csv';

    /**
     * Maxmind source file on Wiki
     */
    const PATH_MAXMIND_LOCATIONS
        = 'https://plumrocket.com/docs/wp-content/uploads/geoip/GeoLite2-City-Locations-en.csv';

    /**
     * @param      $path
     * @param null $store
     * @param null $scope
     * @return mixed
     */
    private function getConfigForCurrentSection($path, $store = null, $scope = null)
    {
        return $this->getConfig(
            Information::CONFIG_SECTION  . '/'. $path,
            $store,
            $scope
        );
    }

    /**
     * @inheritDoc
     */
    public function isModuleEnabled($store = null, $scope = null): bool
    {
        return (bool) $this->getConfigForCurrentSection('general/enabled', $store, $scope);
    }

    /**
     * @return bool
     */
    public function isConfiguredForUse(): bool
    {
        return $this->isModuleEnabled() && $this->getEnableMethodsNumber();
    }

    /**
     * @param int|string $store
     * @return bool
     */
    public function enabledMaxmindGeoIp($store = null)
    {
        return (bool)$this->getConfigForCurrentSection(
            'methods/' . self::MAXMIND_GROUP . '/enabled',
            $store
        );
    }

    /**
     * @param int|string $store
     * @return string
     */
    public function getMaxmindInstallMethod($store = null)
    {
        return $this->getConfigForCurrentSection(
            'methods/' . self::MAXMIND_GROUP . '/install_method',
            $store
        );
    }

    /**
     * @param int|string $store
     * @return bool
     */
    public function enabledIpToCountryGeoIp($store = null)
    {
        return (bool)$this->getConfigForCurrentSection(
            'methods/' . self::IPTOCOUNTRY_GROUP . '/enabled',
            $store
        );
    }

    /**
     * @param int|string $store
     * @return string
     */
    public function getIpToCountryInstallMethod($store = null)
    {
        return $this->getConfigForCurrentSection(
            'methods/' . self::IPTOCOUNTRY_GROUP . '/install_method',
            $store
        );
    }

    /**
     * @param int|string $store
     * @return bool
     */
    public function enabledIpApiGeoIp($store = null)
    {
        return (bool)$this->getConfigForCurrentSection(
            'methods/' . self::IPAPI_GROUP . '/enabled',
            $store
        );
    }

    /**
     * @param null $store
     * @return bool
     */
    private function getIpApiAccessKey($store = null)
    {
        return (string)$this->getConfigForCurrentSection(
            'methods/' . self::IPAPI_GROUP . '/access_key',
            $store
        );
    }

    /**
     * @param $ip
     * @return string
     */
    public function getIpApiUrl($ip = self::DEFAULT_IP, $withAccessKey = true)
    {
        $apiUrl = self::IPAPI_CONNECTION_URL . '/' . $ip;

        if ($withAccessKey) {
            $apiUrl .= '?access_key=' . $this->getIpApiAccessKey();
        }

        return $apiUrl;
    }

    /**
     * @param null $store
     * @return int
     */
    public function getEnableMethodsNumber($store = null)
    {
        $methodsArray = [self::MAXMIND_GROUP, self::IPTOCOUNTRY_GROUP, self::IPAPI_GROUP];
        $count = 0;
        foreach ($methodsArray as $method) {
            if ($this->getConfigForCurrentSection(
                'methods/' . $method . '/enabled',
                $store
            )) {
                $count++;
            }
        }

        return $count;
    }
}
