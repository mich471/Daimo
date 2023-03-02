<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\DataPrivacy\Model;

use Magento\Framework\Model\AbstractModel;
use Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterface;

/**
 * @since 3.1.0
 */
class RemovalRequest extends AbstractModel implements RemovalRequestInterface
{

    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\RemovalRequest::class);
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): string
    {
        return (string) $this->_getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId(): int
    {
        return (int) $this->_getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function getGuestEmail(): string
    {
        return (string) $this->_getData(self::GUEST_EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function getCreatorIp(): string
    {
        return (string) $this->_getData(self::CREATOR_IP);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedBy(): int
    {
        return (int) $this->_getData(self::CREATED_BY);
    }

    /**
     * @inheritDoc
     */
    public function getAdminId(): int
    {
        return (int) $this->_getData(self::ADMIN_ID);
    }

    /**
     * @inheritDoc
     */
    public function getAdminComment(): string
    {
        return (string) $this->_getData(self::ADMIN_COMMENT);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): string
    {
        return (int) $this->_getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function getScheduledAt(): string
    {
        return (int) $this->_getData(self::SCHEDULED_AT);
    }

    /**
     * @inheritDoc
     */
    public function getWebsiteId(): int
    {
        return (int) $this->_getData(self::WEBSITE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setStatus(string $status): RemovalRequestInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedBy(int $type): RemovalRequestInterface
    {
        return $this->setData(self::CREATED_BY, $type);
    }

    /**
     * @inheritDoc
     */
    public function setAdminId(int $adminId): RemovalRequestInterface
    {
        return $this->setData(self::ADMIN_ID, $adminId);
    }

    /**
     * @inheritDoc
     */
    public function setAdminComment(string $comment): RemovalRequestInterface
    {
        return $this->setData(self::ADMIN_COMMENT, $comment);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId(int $customerId): RemovalRequestInterface
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritDoc
     */
    public function setCreatorIp(string $ip): RemovalRequestInterface
    {
        return $this->setData(self::CREATOR_IP, $ip);
    }

    /**
     * @inheritDoc
     */
    public function setGuestEmail(string $email): RemovalRequestInterface
    {
        return $this->setData(self::GUEST_EMAIL, $email);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt(string $dateTime): RemovalRequestInterface
    {
        return $this->setData(self::CREATED_AT, $dateTime);
    }

    /**
     * @inheritDoc
     */
    public function setScheduledAt(string $dateTime): RemovalRequestInterface
    {
        return $this->setData(self::SCHEDULED_AT, $dateTime);
    }

    /**
     * @inheritDoc
     */
    public function setWebsiteId(int $websiteId): RemovalRequestInterface
    {
        return $this->setData(self::WEBSITE_ID, $websiteId);
    }
}
