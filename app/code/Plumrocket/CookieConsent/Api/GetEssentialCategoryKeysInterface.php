<?php
/**
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Api;

/**
 * Retrieve list of category keys with is allowed to use
 *
 * @since 1.0.0
 */
interface GetEssentialCategoryKeysInterface
{
    /**
     * Get essential categories.
     *
     * @return string[]
     */
    public function execute(): array;
}
