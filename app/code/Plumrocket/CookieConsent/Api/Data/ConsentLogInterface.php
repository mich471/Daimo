<?php
/**
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Api\Data;

/**
 * @since 1.0.0
 */
interface ConsentLogInterface
{
    public const DATA_PERSISTOR_KEY = 'pr-cookie-consent-log';

    public const GUEST_EMAIL = 'guest_email';
    public const CUSTOMER_ID = 'customer_id';
    public const WEBSITE_ID = 'website_id';
    public const SETTINGS = 'settings';
    public const CREATED_AT = 'created_at';
    public const IP_ADDRESS = 'ip_address';

    /**
     * Get log id.
     *
     * @return int
     */
    public function getId();

    /**
     * Set log id.
     *
     * @param int $value
     * @return \Plumrocket\CookieConsent\Api\Data\ConsentLogInterface
     */
    public function setId($value);

    /**
     * Return 0 for guests consent
     *
     * @return int
     */
    public function getCustomerId(): int;

    /**
     * Return empty string for customers consent
     *
     * @return string
     */
    public function getGuestEmail(): string;

    /**
     * Get website id.
     *
     * @return int
     */
    public function getWebsiteId(): int;

    /**
     * List of allowed categories or *
     *
     * @return array
     */
    public function getSettings(): array;

    /**
     * Get visitor IP address.
     *
     * @return string
     */
    public function getIpAddress(): string;

    /**
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * @param int $customerId
     * @return \Plumrocket\CookieConsent\Api\Data\ConsentLogInterface
     */
    public function setCustomerId(int $customerId): ConsentLogInterface;

    /**
     * @param string $guestEmail
     * @return \Plumrocket\CookieConsent\Api\Data\ConsentLogInterface
     */
    public function setGuestEmail(string $guestEmail): ConsentLogInterface;

    /**
     * @param int $websiteId
     * @return \Plumrocket\CookieConsent\Api\Data\ConsentLogInterface
     */
    public function setWebsiteId(int $websiteId): ConsentLogInterface;

    /**
     * @param array $settings
     * @return \Plumrocket\CookieConsent\Api\Data\ConsentLogInterface
     */
    public function setSettings(array $settings): ConsentLogInterface;

    /**
     * @param string $ipAddress
     * @return \Plumrocket\CookieConsent\Api\Data\ConsentLogInterface
     */
    public function setIpAddress(string $ipAddress): ConsentLogInterface;

    /**
     * @param string $gmtDateTime
     * @return \Plumrocket\CookieConsent\Api\Data\ConsentLogInterface
     */
    public function setCreatedAt(string $gmtDateTime): ConsentLogInterface;
}
