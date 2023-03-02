<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Api;

/**
 * Allow check module status and disable it
 *
 * @since 2.3.7
 * @deprecated since 2.5.0
 */
interface ProductStatusManagerInterface
{
    /**
     * @param string $moduleName can be either "Plumrocket_SocialLoginFree" or "SocialLoginFree"
     * @return bool
     */
    public function isEnabled(string $moduleName): bool;

    /**
     * @param string $moduleName can be either "Plumrocket_SocialLoginFree" or "SocialLoginFree"
     * @return bool
     */
    public function disable(string $moduleName): bool;
}
