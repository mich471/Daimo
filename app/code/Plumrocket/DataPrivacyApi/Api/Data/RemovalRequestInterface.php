<?php
/**
 * @package     Plumrocket_DataPrivacyApi
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\DataPrivacyApi\Api\Data;

/**
 * @since 2.1.0
 */
interface RemovalRequestInterface
{

    public const CREATED_BY_ADMIN = 1;
    public const CREATED_BY_CUSTOMER = 0;

    public const STATUS = 'status';
    public const CREATED_AT = 'created_at';
    public const SCHEDULED_AT = 'scheduled_at';
    public const CREATED_BY = 'created_by';
    public const ADMIN_ID = 'admin_id';
    public const ADMIN_COMMENT = 'admin_comment';
    public const CUSTOMER_ID = 'customer_id';
    public const CREATOR_IP = 'customer_ip';
    public const GUEST_EMAIL = 'customer_email';
    public const WEBSITE_ID = 'website_id';

    /**
     * Get request status.
     *
     * @return string
     */
    public function getStatus(): string;

    /**
     * Get who created this request.
     *
     * @return int
     */
    public function getCreatedBy(): int;

    /**
     * Get admin id if request created by admin.
     *
     * @return int
     */
    public function getAdminId(): int;

    /**
     * Get comment that admin left during request creating.
     *
     * @return string
     */
    public function getAdminComment(): string;

    /**
     * Get id of customer to delete.
     *
     * @return int returns 0 for guests.
     */
    public function getCustomerId(): int;

    /**
     * Get email of guest to delete.
     *
     * @return string
     */
    public function getGuestEmail(): string;

    /**
     * Get IP of customer/guest/admin who created request.
     *
     * @return string
     */
    public function getCreatorIp(): string;

    /**
     * Get request creating time.
     *
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * Get time when customer should be deleted.
     *
     * @return string
     */
    public function getScheduledAt(): string;

    /**
     * Get customer website id.
     *
     * @return int
     */
    public function getWebsiteId(): int;

    /**
     * Set request status.
     *
     * @param string $status
     * @return RemovalRequestInterface
     */
    public function setStatus(string $status): RemovalRequestInterface;

    /**
     * Set who created this request.
     *
     * @param int $type
     * @return RemovalRequestInterface
     */
    public function setCreatedBy(int $type): RemovalRequestInterface;

    /**
     * Set admin id who created request.
     *
     * @param int $adminId
     * @return \Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterface
     */
    public function setAdminId(int $adminId): RemovalRequestInterface;

    /**
     * Set comment that admin left during request creating.
     *
     * @param string $comment
     * @return \Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterface
     */
    public function setAdminComment(string $comment): RemovalRequestInterface;

    /**
     * Set customer id whose information we need to delete.
     *
     * @param int $customerId
     * @return \Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterface
     */
    public function setCustomerId(int $customerId): RemovalRequestInterface;

    /**
     * Set guest email whose information we need to delete.
     *
     * @param string $email
     * @return \Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterface
     */
    public function setGuestEmail(string $email): RemovalRequestInterface;

    /**
     * Set IP of customer/guest/admin who create request.
     *
     * @param string $ip
     * @return \Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterface
     */
    public function setCreatorIp(string $ip): RemovalRequestInterface;

    /**
     * Set request creating time.
     *
     * @param string $dateTime
     * @return \Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterface
     */
    public function setCreatedAt(string $dateTime): RemovalRequestInterface;

    /**
     * Set time when customer should be deleted.
     *
     * @param string $dateTime
     * @return \Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterface
     */
    public function setScheduledAt(string $dateTime): RemovalRequestInterface;

    /**
     * Set customer website id.
     *
     * @param int $websiteId
     * @return \Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterface
     */
    public function setWebsiteId(int $websiteId): RemovalRequestInterface;
}
