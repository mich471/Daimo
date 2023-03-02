<?php
/**
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Api;

/**
 * Retrieve category id by its key
 *
 * @since 1.0.0
 */
interface GetCategoryIdByKeyInterface
{
    /**
     * @param string $key
     * @param bool   $forceReload
     * @return int
     */
    public function execute(string $key, bool $forceReload = false): int;
}
