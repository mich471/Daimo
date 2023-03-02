<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Helper;

use Plumrocket\Base\Helper\AbstractConfig;

/**
 * @since 1.0.0
 */
class Config extends AbstractConfig
{
    /**
     * Check if export is allowed.
     *
     * @param string|int|null $store
     * @param string|null     $scope
     * @return bool
     */
    public function isAccountExportEnabled($store = null, $scope = null): bool
    {
        return $this->isModuleEnabled($store, $scope);
    }

    /**
     * Check if deletion is allowed.
     *
     * @param string|int|null $store
     * @param string|null     $scope
     * @return bool
     */
    public function isAccountDeletionEnabled($store = null, $scope = null): bool
    {
        return $this->isModuleEnabled($store, $scope);
    }

    /**
     * Get account delete deletion.
     *
     * @param string|int|null $store
     * @param string|null     $scope
     * @return int
     */
    public function getDeletionTime($store = null, $scope = null): int
    {
        return $this->isInstantRemovalRequest() ? 0 : 24 * 60 * 60;
    }

    /**
     * Change to true to be able to remove customer data right after creating removal request.
     *
     * @return bool
     */
    private function isInstantRemovalRequest(): bool
    {
        return false;
    }
}
