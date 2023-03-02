<?php
/**
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\CookieConsent\Api;

/**
 * Check if we can manage cookie for current guest/customer by location
 *
 * @since 1.0.0
 */
interface CanManageCookieInterface
{
    /**
     * GeoIPLookup will extend this method
     *
     * @return bool
     */
    public function execute(): bool;
}
