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

namespace Plumrocket\GDPR\Helper\Geo;

use Plumrocket\GDPR\Helper\Data as DataHelper;
use Plumrocket\GDPR\Model\Config\Source\GeoIPRestrictions;

/**
 * @deprecated since 2.0.0
 */
class Location extends \Plumrocket\GDPR\Helper\Main
{
    /**
     * Name of Geo IP module
     */
    const GEO_IP_MODULE_NAME = 'GeoIPLookup';

    /**
     * Geo IP Module key
     */
    const GEO_IP_MODULE_KEY = 'prgeoiplookup';

    /**
     * Geo IP model class
     */
    const GEO_IP_MODEL_PATH = '\Plumrocket\GeoIPLookup\Model\GeoIPLookup';

    /**
     * Geo IP Helper class
     */
    const GEO_IP_HELPER_PATH = '\Plumrocket\GeoIPLookup\Helper\Config';

    /**
     * Name of method for GeoIPLookup Helper that retrieving GeoIP method identifier
     */
    const GEO_IP_HELPER_METHOD_NAME = 'getEnableMethodsNumber';

    const STATUS_ABSENT = 0;

    const STATUS_INSTALLED = 1;

    const STATUS_ENABLED = 2;

    const STATUS_NOT_CONFIGURED = 3;

    const USA_CCPA = 'US';

    const REGION_CCPA = 'California';

    /**
     * @var string
     */
    private $geoIpModuleName = self::GEO_IP_MODULE_NAME;

    /**
     * @var string
     */
    public $geoIpModuleKey = self::GEO_IP_MODULE_KEY;

    /**
     * @var string
     */
    public $geoIpModelPath = self::GEO_IP_MODEL_PATH;

    /**
     * @var string
     */
    public $geoIpHelperPath = self::GEO_IP_HELPER_PATH;

    /**
     * @var string
     */
    public $geoIpHelperMethodName = self::GEO_IP_HELPER_METHOD_NAME;

    /**
     * @var array
     */
    private $requiredMethods = [
        'isInEuropeanUnion',
        'getCountryCode',
    ];

    /**
     * @var array
     */
    private $ccpa;

    /**
     * @var bool|null
     */
    private $canUseGeoIP;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    private $backendUrl;

    /**
     * Location constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\UrlInterface $backendUrl
    ) {
        $this->backendUrl = $backendUrl;
        parent::__construct($objectManager, $context);
    }

    /**
     * @return bool
     */
    private function isGeoIpModuleExists()
    {
        return false !== $this->moduleExists($this->geoIpModuleName);
    }

    /**
     * @return bool
     */
    private function isGeoIpModuleEnabled()
    {
        return 2 == $this->moduleExists($this->geoIpModuleName);
    }

    /**
     * @return bool
     */
    public function getGeoIpModuleStatus()
    {
        if (2 === $this->moduleExists($this->geoIpModuleName) && ! $this->canUseGeoIP()) {
            return self::STATUS_NOT_CONFIGURED;
        }

        return (int) $this->moduleExists($this->geoIpModuleName);
    }

    /**
     * @return null|\Plumrocket\GeoIPLookup\Model\GeoLocation
     */
    private function getGeoIpModel()
    {
        return $this->isGeoIpModuleEnabled()
            ? $this->_objectManager->create($this->geoIpModelPath)
            : null;
    }

    /**
     * @return null|\Plumrocket\GeoIPLookup\Helper\Data
     */
    private function getGeoIpHelper()
    {
        return $this->isGeoIpModuleEnabled()
            ? $this->_objectManager->create($this->geoIpHelperPath)
            : null;
    }

    /**
     * @return bool
     */
    public function canUseGeoIP()
    {
        if (null === $this->canUseGeoIP) {
            $this->canUseGeoIP = $this->isGeoIpModuleEnabled()
                && $this->isValidLocationModel()
                && (0 !== $this->getGeoIpMethodIdentifier());
        }

        return $this->canUseGeoIP;
    }

    /**
     * @return bool
     */
    public function getGeoIpMethodIdentifier()
    {
        $geoIpHelper = $this->getGeoIpHelper();
        $methodName = $this->geoIpHelperMethodName;

        if (! $geoIpHelper || ! method_exists($geoIpHelper, $methodName)) {
            return false;
        }

        return $geoIpHelper->$methodName();
    }

    /**
     * @return bool
     */
    private function isValidLocationModel()
    {
        $model = $this->getGeoIpModel();

        if (! $model) {
            return false;
        }

        foreach ($this->requiredMethods as $method) {
            if (! method_exists($model, $method)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isPassCookieGeoIPRestriction()
    {
        if (! $this->canUseGeoIP()) {
            return true;
        }

        $geoIpModel = $this->getGeoIpModel();

        if (is_object($geoIpModel)) {
            return $this->validateLocation(
                $this->getCookieGeoIPRestriction(),
                static function () use ($geoIpModel) {
                    return [
                        $geoIpModel->getCountryCode(),
                        $geoIpModel->getCurrentCountryState(),
                        $geoIpModel->isInEuropeanUnion(),
                    ];
                }
            );
        }

        return false;
    }

    /**
     * @param \Plumrocket\GDPR\Api\Data\CheckboxInterface $checkbox
     * @return bool
     */
    public function isPassCheckboxGeoIPRestriction($checkbox)
    {
        if (! $this->canUseGeoIP()) {
            return true;
        }

        $restrictions = [];
        $this->ccpa = [];

        if (! empty($checkbox->getGeoTargeting())
            && is_array($checkbox->getGeoTargeting())
        ) {
            foreach ($checkbox->getGeoTargeting() as $item) {
                if (is_array($item)) {
                    $restrictions = array_merge($restrictions, array_values($item));
                } elseif (is_string($item)) {
                    if ($explodeItem = explode(',', $item)) {
                        $restrictions = array_merge($restrictions, array_values($explodeItem));
                        continue;
                    }

                    $restrictions[] = $item;
                }
            }

            if (! empty($checkbox->getGeoTargetingUsaStates())
                && is_array($checkbox->getGeoTargetingUsaStates())
            ) {
                foreach ($checkbox->getGeoTargetingUsaStates() as $item) {
                    if (is_array($item)) {
                        $this->ccpa = array_merge($this->ccpa, array_values($item));
                    } else {
                        $this->ccpa[] = $item;
                    }
                }
            }

            $restrictions = array_unique($restrictions);
        }

        $geoIpModel = $this->getGeoIpModel();

        return $this->validateLocation(
            $restrictions,
            static function () use ($geoIpModel) {
                return [
                    $geoIpModel->getCountryCode(),
                    $geoIpModel->getCurrentCountryState(),
                    $geoIpModel->isInEuropeanUnion()
                ];
            }
        );
    }

    /**
     * @param $options
     * @param callable $getGeoIpInfo
     * @return bool
     */
    public function validateLocation($options, callable $getGeoIpInfo)
    {
        if (empty($options)
            || in_array(GeoIPRestrictions::ALL, $options, true)
        ) {
            return true;
        }

        list ($countryCode, $currentCountryState, $isInEU) = $getGeoIpInfo();

        if ($countryCode) {
            if (in_array(GeoIPRestrictions::EU, $options, true) && $isInEU) {
                return true;
            }

            if (in_array($countryCode, $options, true)) {
                if ($countryCode === self::USA_CCPA) {

                    if (empty($this->ccpa)
                        || in_array(GeoIPRestrictions::ALL, $this->ccpa, true)
                    ) {
                        return true;
                    }

                    if ($currentCountryState) {

                        if (in_array($currentCountryState, $this->ccpa, true)) {
                            return true;
                        }
                    }

                    return false;
                }

                return true;
            }
        } elseif (in_array(GeoIPRestrictions::UNKNOWN, $options, true)) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve config values
     *
     * @param int|string $store
     * @return array
     */
    public function getCookieGeoIPRestriction($store = null)
    {
        $result = [];
        $configValue = $this->getConfig(
            DataHelper::SECTION_ID . '/cookie_consent/geoip_restriction',
            $store
        );

        $configValueCcpa = $this->getConfig(
            DataHelper::SECTION_ID . '/cookie_consent/geoip_restriction_usa_ccpa',
            $store
        );

        if (! empty($configValue)) {
            $configData = explode(',', $configValue);

            if (is_array($configData)) {
                $result = array_merge($result, $configData);
            }
        }

        if (! empty($configValueCcpa)) {
            $this->ccpa = explode(',', $configValueCcpa);
        }

        return $result;
    }
}
