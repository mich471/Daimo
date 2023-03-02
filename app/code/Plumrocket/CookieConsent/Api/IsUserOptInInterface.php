<?php
/**
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Api;

/**
 * @since 1.0.0
 */
interface IsUserOptInInterface
{

    /**
     * Is user opt in.
     *
     * @return bool
     */
    public function execute(): bool;
}
