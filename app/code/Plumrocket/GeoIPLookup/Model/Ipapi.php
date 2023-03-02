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

class Ipapi
{
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    private $curl;

    /**
     * @var string
     */
    private $dataName = 'Ipapi.Com';

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \Plumrocket\GeoIPLookup\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Plumrocket\GeoIPLookup\Model\Cache\GeoIpInterface
     */
    private $geoIpCache;

    /**
     * @param \Magento\Framework\HTTP\Client\Curl                  $curl
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Magento\Framework\Json\Helper\Data                  $jsonHelper
     * @param \Plumrocket\GeoIPLookup\Helper\Config                $configHelper
     * @param \Plumrocket\GeoIPLookup\Model\Cache\GeoIpInterface   $geoIpCache
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Plumrocket\GeoIPLookup\Helper\Config $configHelper,
        \Plumrocket\GeoIPLookup\Model\Cache\GeoIpInterface $geoIpCache
    ) {
        $this->curl = $curl;
        $this->remoteAddress = $remoteAddress;
        $this->jsonHelper = $jsonHelper;
        $this->configHelper = $configHelper;
        $this->geoIpCache = $geoIpCache;
    }

    /**
     * @param int $ip
     * @return array|null
     */
    public function getGeoLocation($ip = 0)
    {
        $result = null;

        if ($ip === 0) {
            $ip = $this->remoteAddress->getRemoteAddress();
        }

        if ($ip) {
            $cacheKey = ip2long($ip) . '_gl_ipapi';
            $geoipCache = $this->geoIpCache->get();

            if (isset($geoipCache[$cacheKey])) {
                return $geoipCache[$cacheKey];
            }

            $curlResult = $this->curlExec($ip);

            if ($curlResult) {
                $curlResultArr = $this->jsonHelper->jsonDecode($curlResult);

                if (isset($curlResultArr['error'])) {
                    return $result;
                } else {
                    $result = [
                        'country_code'  => isset($curlResultArr['country_code'])
                            ? $curlResultArr['country_code']
                            : '',
                        'country_name'  => isset($curlResultArr['country_name'])
                            ? $curlResultArr['country_name']
                            : '',
                        'region_name'  => isset($curlResultArr['region_name'])
                            ? $curlResultArr['region_name']
                            : '',
                        'city_name'     => isset($curlResultArr['city'])
                            ? $curlResultArr['city']
                            : '',
                        'latitude'      => isset($curlResultArr['latitude'])
                            ? $curlResultArr['latitude']
                            : '',
                        'longitude'     => isset($curlResultArr['longitude'])
                            ? $curlResultArr['longitude']
                            : '',
                        'database_name' => $this->dataName
                    ];
                }
            }

            $result = (!empty($result)) ? $result : null;
            $geoipCache[$cacheKey] = $result;
            $this->geoIpCache->set($geoipCache);
        }

        return $result;
    }

    /**
     * @param int $ip
     * @return null
     */
    public function getCountryCode($ip = 0)
    {
        $result = null;
        if ($ip === 0) {
            $ip = $this->remoteAddress->getRemoteAddress();
        }

        if ($ip) {
            $cacheKey = ip2long($ip) . '_cc_ipapi';
            $geoipCache = $this->geoIpCache->get();
            if (isset($geoipCache[$cacheKey])) {
                return $geoipCache[$cacheKey];
            }

            $curlResult = $this->curlExec($ip);

            if ($curlResult) {
                $curlResultArr = $this->jsonHelper->jsonDecode($curlResult);
                if (isset($curlResultArr['error'])) {
                    return $result;
                } else {
                    $result = $curlResultArr['country_code'];
                }
            }

            $result = (!empty($result)) ? $result : null;
            $geoipCache[$cacheKey] = $result;
            $this->geoIpCache->set($geoipCache);
        }

        return $result;
    }

    /**
     * @param $countryCode
     * @return bool
     */
    public function hasCountry($countryCode)
    {
        $result = false;
        if ($countryCode) {
            $result = true;
        }

        return $result;
    }

    /**
     * @param int $ip
     * @return |null
     */
    public function getCurrentCountryState($ip = 0)
    {
        $result = null;
        if ($ip === 0) {
            $ip = $this->remoteAddress->getRemoteAddress();
        }

        if ($ip) {
            $cacheKey = ip2long($ip) . '_cc_ipapi_cstate';
            $geoipCache = $this->geoIpCache->get();

            if (isset($geoipCache[$cacheKey])) {
                return $geoipCache[$cacheKey];
            }

            $curlResult = $this->curlExec($ip);

            if ($curlResult) {
                $curlResultArr = $this->jsonHelper->jsonDecode($curlResult);
                if (isset($curlResultArr['error'])) {
                    return $result;
                } else {
                    $result = $curlResultArr['region_name'];
                }
            }

            $result = (!empty($result)) ? $result : null;
            $geoipCache[$cacheKey] = $result;
            $this->geoIpCache->set($geoipCache);
        }

        return $result;
    }

    /**
     * @param $ip
     * @return string
     */
    public function curlExec($ip)
    {
        $url = "http://" . $this->configHelper->getIpApiUrl($ip);
        $options = [
            CURLOPT_HEADER => 0,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 4,
        ];
        $this->curl->post($url, $options);

        return $this->curl->getBody();
    }
}
