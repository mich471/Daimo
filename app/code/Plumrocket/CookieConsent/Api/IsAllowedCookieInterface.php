<?php
/**
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\CookieConsent\Api;

/**
 * @since 1.0.0
 */
interface IsAllowedCookieInterface
{

    /**
     * Is allowed cookie.
     *
     * @param string $name
     * @return bool
     */
    public function execute(string $name): bool;
}
