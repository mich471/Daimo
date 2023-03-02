<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Api;

/**
 * Allow easily retrieve status of Plumrocket extension
 *
 * @since 2.3.9
 */
interface GetExtensionStatusInterface
{
    /**
     * Not installed
     * @deprecated since 2.4.1
     * @see \Plumrocket\Base\Api\ExtensionStatusInterface::NOT_INSTALLED
     */
    const MODULE_STATUS_NOT_INSTALLED = ExtensionStatusInterface::NOT_INSTALLED;

    /**
     * Installed but disabled in system config
     * @deprecated since 2.4.1
     * @see \Plumrocket\Base\Api\ExtensionStatusInterface::DISABLED
     */
    const MODULE_STATUS_DISABLED = ExtensionStatusInterface::DISABLED;

    /**
     * Installed, enabled in CLI and system config
     * @deprecated since 2.4.1
     * @see \Plumrocket\Base\Api\ExtensionStatusInterface::ENABLED
     */
    const MODULE_STATUS_ENABLED = ExtensionStatusInterface::ENABLED;

    /**
     * Retrieve status of Plumrocket module
     *
     * @param string $moduleName e.g. SocialLoginFree
     * @return int
     */
    public function execute(string $moduleName): int;
}
