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
interface IsAllowedCategoryInterface
{

    /**
     * Is allowed category.
     *
     * @param string $key
     * @return bool
     */
    public function execute(string $key): bool;
}
