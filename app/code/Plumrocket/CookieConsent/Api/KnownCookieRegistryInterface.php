<?php
/**
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Api;

/**
 * Retrieve known cookies name and their information
 *
 * @since 1.0.0
 */
interface KnownCookieRegistryInterface
{

    /**
     * Get list of know cookies.
     *
     * @return array[]
     */
    public function getList(): array;
}
