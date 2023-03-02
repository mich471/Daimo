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
interface GeoLocationValidatorInterface
{
    /**
     * @param array       $regions
     * @param array       $counties
     * @param array       $usaStates
     * @param string|null $ip
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException if extension isn't ready to look up
     */
    public function validate(array $regions, array $counties, array $usaStates = [], string $ip = null): bool;

    /**
     * @param array       $options regions and countries
     * @param array       $usaStates
     * @param string|null $ip
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateByMergedOptions(array $options, array $usaStates = [], string $ip = null): bool;
}
