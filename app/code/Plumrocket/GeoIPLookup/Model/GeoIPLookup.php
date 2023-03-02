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

namespace Plumrocket\GeoIPLookup\Model;

class GeoIPLookup
{
    /**
     * @var string
     */
    const MAXMIND = 'maxmind';

    /**
     * @var string
     */
    const IPTOCOUNTRY = 'iptocountry';

    /**
     * @var string
     */
    const IPAPI = 'ipapi';

    /**
     * @var array
     */
    private $europeanUnionCountryIsoCodes = [
        "CY","GR","EE","BG","LV","LT","SE","FI","AX","RO","HU","SK","PL","YT","RE","PT","GI","ES",
        "IT","MT","AT", "DK","GB","IE","NL","BE","DE","LU","FR","CZ","HR","SI","MQ","GP","MF"
    ];

    /**
     * @var null
     */
    private $geoIpModels = [];

    /**
     * @var \Plumrocket\GeoIPLookup\Helper\Config
     */
    private $config;

    /**
     * @var Maxmind
     */
    private $maxmind;

    /**
     * @var IpToCountry
     */
    private $ipToCountry;

    /**
     * @var Ipapi
     */
    private $ipapi;

    /**
     * GeoIPLookup constructor.
     *
     * @param \Plumrocket\GeoIPLookup\Helper\Config $config
     * @param Maxmind                               $maxmind
     * @param IpToCountry                           $ipToCountry
     * @param Ipapi                                 $ipapi
     */
    public function __construct(
        \Plumrocket\GeoIPLookup\Helper\Config $config,
        \Plumrocket\GeoIPLookup\Model\Maxmind $maxmind,
        \Plumrocket\GeoIPLookup\Model\IpToCountry $ipToCountry,
        \Plumrocket\GeoIPLookup\Model\Ipapi $ipapi
    ) {
        $this->config = $config;
        $this->maxmind = $maxmind;
        $this->ipToCountry = $ipToCountry;
        $this->ipapi = $ipapi;
    }

    /**
     * @return bool
     */
    public function canUse()
    {
        return $this->config->isConfiguredForUse();
    }

    /**
     * @param int    $value
     * @param string $key
     * @return bool|null
     */
    public function isInEuropeanUnion($value = 0, $key = 'ip', $priorityOfServices = [])
    {
        if ($key === 'ip') {
            $countryCode = $this->getCountryCode($value, $priorityOfServices);
            if (!$countryCode) {
                return null;
            }
        } elseif ($key === 'country_code' && $this->hasCountry($value, $priorityOfServices)) {
            $countryCode = $value;
        } else {
            return null;
        }

        return in_array($countryCode, $this->europeanUnionCountryIsoCodes);
    }

    /**
     * @param int $ip
     * @return array|string
     */
    public function getGeoLocation($ip = 0, $priorityOfServices = [])
    {
        $result = null;
        $geoIpModels = $this->getGeoIpModels($priorityOfServices);

        if ($geoIpModels) {
            foreach ($geoIpModels as $geoIpModel) {
                $result = $geoIpModel->getGeoLocation($ip);
                if ($result) {
                    break;
                }

            }
        }

        if (!empty($result) && !isset($result['is_in_european_union'])) {
            $result['is_in_european_union'] = $this->isInEuropeanUnion($ip);
        }

        return (!empty($result)) ? $result : __('No records found')->getText();
    }

    /**
     * @param int $ip
     * @return null
     */
    public function getCountryCode($ip = 0, $priorityOfServices = [])
    {
        $result = null;
        $geoIpModels = $this->getGeoIpModels($priorityOfServices);
        foreach ($geoIpModels as $geoIpModel) {
            $result = $geoIpModel->getCountryCode($ip);
            if ($result) {
                break;
            }
        }

        return (!empty($result)) ? $result : null;
    }

    /**
     * @param $countryCode
     * @return null
     */
    public function hasCountry($countryCode, $priorityOfServices = [])
    {
        $result = null;
        $geoIpModels = $this->getGeoIpModels($priorityOfServices);
        foreach ($geoIpModels as $geoIpModel) {
            $result = $geoIpModel->hasCountry($countryCode);
            if ($result) {
                break;
            }
        }

        return $result;
    }

    /**
     * @return array|null
     */
    public function getGeoIpModels($priorityOfServices = [])
    {
        $serviceItems = [];

        if (! $this->config->isModuleEnabled()) {
            return null;
        }

        if (!empty($this->geoIpModels)) {
            return $this->geoIpModels;
        }

        $services = [
            self::MAXMIND     => [
                'model' => $this->maxmind,
                'enabled' => $this->config->enabledMaxmindGeoIp()
            ],
            self::IPTOCOUNTRY => [
                'model' => $this->ipToCountry,
                'enabled' => $this->config->enabledIpToCountryGeoIp()
            ],
            self::IPAPI       => [
                'model' => $this->ipapi,
                'enabled' => $this->config->enabledIpApiGeoIp()
            ]
        ];

        if (!empty($priorityOfServices)) {
            foreach ($priorityOfServices as $sId) {
                if (!empty($services[$sId])) {
                    $serviceItems[$sId] = $services[$sId];
                }
            }
        } else {
            $serviceItems = $services;
        }

        foreach ($serviceItems as $service) {
            if ($service['enabled']) {
                $this->geoIpModels[] = $service['model'];
            }
        }

        return $this->geoIpModels;
    }

    /**
     * @param $ip
     * @return array
     */
    public function geoIpLookupTest($ip)
    {
        $fields = [
            'continent_code'         => __('Continent'),
            'continent_name'         => __('Continent Code'),
            'country_name'           => __('Country'),
            'country_code'           => __('Country Code'),
            'region_name'            => __('Region Name'),
            'city_name'              => __('City'),
            'postal_code'            => __('Postal Code'),
            'time_zone'              => __('Time Zone'),
            'is_in_european_union'   => __('Is In European Union'),
            'latitude'               => __('Latitude'),
            'longitude'              => __('Longitude'),
            'metro_code'             => __('Metro Code'),
            'subdivision_1_iso_code' => __('Subdivision 1 Iso Code'),
            'subdivision_1_name'     => __('Subdivision 1 Name'),
            'subdivision_2_iso_code' => __('Subdivision 2 Iso Code'),
            'subdivision_2_name'     => __('Subdivision 2 Name'),
            'database_name'          => __('Database Name')
        ];

        $result = [];
        $text = '';

        $geoData = $this->getGeoLocation($ip);

        if (is_array($geoData)) {
            $geoData['is_in_european_union'] = ($geoData['is_in_european_union'] === true) ? 'true' : 'false';
            foreach ($fields as $key => $value) {
                if (array_key_exists($key, $geoData) && !empty($geoData[$key])) {
                    $text .= $value . ": " . $geoData[$key] . "\n";
                }
            }
            if (!empty($geoData['latitude']) && !empty($geoData['longitude'])) {
                $result['latitude'] = $geoData['latitude'];
                $result['longitude'] = $geoData['longitude'];
            } elseif (!empty($geoData['country_name'])) {
                $result['country_name'] = $geoData['country_name'];
            }
        } else {
            $text = $geoData;
        }
        $result['text'] = $text;

        return  $result;
    }

    /**
     * @param int   $ip
     * @param array $priorityOfServices
     * @return null
     */
    public function getCurrentCountryState($ip = 0, $priorityOfServices = [])
    {
        $result = null;
        $geoIpModels = $this->getGeoIpModels($priorityOfServices);

        foreach ($geoIpModels as $geoIpModel) {
            $result = $geoIpModel->getCurrentCountryState($ip);
            if ($result) {
                break;
            }
        }

        return (!empty($result)) ? $result : null;
    }
}
