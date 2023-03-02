<?php
/**
 * @package     Plumrocket_magento2.3.6
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model\Guest;

use Plumrocket\DataPrivacy\Helper\Config\PrivacyCenterDashboard;
use Plumrocket\Token\Api\TypeInterface;

/**
 * @since 3.1.0
 */
class PrivacyCenterToken implements TypeInterface
{
    /**
     * Token key
     */
    const KEY = 'prgdpr_guest_privacy_center';

    /**
     * Expiration Days
     */
    const DEFAULT_EXPIRATION_DAYS = 3;

    /**
     * @var \Plumrocket\DataPrivacy\Helper\Config\PrivacyCenterDashboard
     */
    private $privacyCenterDashboardConfig;

    public function __construct(PrivacyCenterDashboard $privacyCenterDashboardConfig)
    {
        $this->privacyCenterDashboardConfig = $privacyCenterDashboardConfig;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return self::KEY;
    }

    /**
     * Retrieve time in seconds
     *
     * @return int
     */
    public function getLifetime(): int
    {
        return strtotime("{$this->getLifetimeDays()} day", 0);
    }

    /**
     * Retrieve time in days
     *
     * @return int
     */
    public function getLifetimeDays(): int
    {
        $days = $this->privacyCenterDashboardConfig->getGuestLinkExpiration();

        if (! $days) {
            $days = self::DEFAULT_EXPIRATION_DAYS;
        }

        return $days;
    }
}
