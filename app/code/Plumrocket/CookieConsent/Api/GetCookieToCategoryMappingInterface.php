<?php
/**
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Api;

/**
 * Retrieve mapping in followed format:
 * [
 *      <cookie_name1> => <category_key1>,
 *      <cookie_name2> => <category_key1>,
 *      <cookie_name3> => <category_key2>,
 * ]
 *
 * @since 1.0.0
 */
interface GetCookieToCategoryMappingInterface
{
    /**
     * Get mapping.
     *
     * @return string[]
     */
    public function execute(): array;
}
