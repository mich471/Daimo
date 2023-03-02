<?php
/**
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Api;

/**
 * Retrieve cookie id by its name
 *
 * @since 1.0.0
 */
interface GetCookieIdByNameInterface
{
    /**
     * Get cookie id by name.
     *
     * @param string $name
     * @param bool   $forceReload
     * @return int
     */
    public function execute(string $name, bool $forceReload = false): int;
}
