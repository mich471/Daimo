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

namespace Plumrocket\GeoIPLookup\Plugin\Plumrocket;

use Magento\Framework\Exception\LocalizedException;
use Plumrocket\GeoIPLookup\Api\GeoLocationValidatorInterface;
use Plumrocket\GeoIPLookup\Helper\Config;
use Plumrocket\GeoIPLookup\Helper\Config\CookieConsentGeo as CookieConsentGeoConfigs;

/**
 * @since 1.2.2
 */
class CanManageCookie
{
    /**
     * @var \Plumrocket\GeoIPLookup\Helper\Config
     */
    private $config;

    /**
     * @var \Plumrocket\GeoIPLookup\Api\GeoLocationValidatorInterface
     */
    private $geoLocationValidator;

    /**
     * @var \Plumrocket\GeoIPLookup\Helper\Config\CookieConsentGeo
     */
    private $cookieConsentGeoConfig;

    /**
     * @param \Plumrocket\GeoIPLookup\Helper\Config                     $config
     * @param \Plumrocket\GeoIPLookup\Api\GeoLocationValidatorInterface $geoLocationValidator
     * @param \Plumrocket\GeoIPLookup\Helper\Config\CookieConsentGeo    $cookieConsentGeoConfig
     */
    public function __construct(
        Config $config,
        GeoLocationValidatorInterface $geoLocationValidator,
        CookieConsentGeoConfigs $cookieConsentGeoConfig
    ) {
        $this->config = $config;
        $this->geoLocationValidator = $geoLocationValidator;
        $this->cookieConsentGeoConfig = $cookieConsentGeoConfig;
    }

    /**
     * @param \Plumrocket\CookieConsent\Api\CanManageCookieInterface $subject
     * @param bool                                                   $result
     * @return bool
     */
    public function afterExecute(\Plumrocket\CookieConsent\Api\CanManageCookieInterface $subject, bool $result)
    {
        if ($result && $this->config->isConfiguredForUse()) {
            try {
                return $this->geoLocationValidator->validateByMergedOptions(
                    $this->cookieConsentGeoConfig->getGeoTargeting(),
                    $this->cookieConsentGeoConfig->getStatesGeoTargeting()
                );
            } catch (LocalizedException $e) {
                return true;
            }
        }

        return $result;
    }
}
