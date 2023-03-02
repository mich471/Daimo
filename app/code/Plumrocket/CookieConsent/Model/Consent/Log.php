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
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Model\Consent;

use Magento\Framework\Model\AbstractModel;
use Plumrocket\CookieConsent\Api\Data\ConsentLogInterface;
use Plumrocket\CookieConsent\Model\ResourceModel\ConsentLog as ConsentLogResource;

/**
 * @since 1.0.0
 */
class Log extends AbstractModel implements ConsentLogInterface
{
    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ConsentLogResource::class);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId(): int
    {
        return (int) $this->_getData(ConsentLogInterface::CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function getGuestEmail(): string
    {
        return (string) $this->_getData(ConsentLogInterface::GUEST_EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function getWebsiteId(): int
    {
        return (int) $this->_getData(ConsentLogInterface::WEBSITE_ID);
    }

    /**
     * @inheritDoc
     */
    public function getSettings(): array
    {
        return (array) $this->_getData(ConsentLogInterface::SETTINGS);
    }

    /**
     * @inheritDoc
     */
    public function getIpAddress(): string
    {
        return (string) $this->_getData(ConsentLogInterface::IP_ADDRESS);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): string
    {
        return (string) $this->_getData(ConsentLogInterface::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId(int $customerId): ConsentLogInterface
    {
        return $this->setData(ConsentLogInterface::CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritDoc
     */
    public function setGuestEmail(string $guestEmail): ConsentLogInterface
    {
        return $this->setData(ConsentLogInterface::GUEST_EMAIL, $guestEmail);
    }

    /**
     * @inheritDoc
     */
    public function setWebsiteId(int $websiteId): ConsentLogInterface
    {
        return $this->setData(ConsentLogInterface::WEBSITE_ID, $websiteId);
    }

    /**
     * @inheritDoc
     */
    public function setSettings(array $settings): ConsentLogInterface
    {
        return $this->setData(ConsentLogInterface::SETTINGS, $settings);
    }

    /**
     * @inheritDoc
     */
    public function setIpAddress(string $ipAddress): ConsentLogInterface
    {
        return $this->setData(ConsentLogInterface::IP_ADDRESS, $ipAddress);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt(string $gmtDateTime): ConsentLogInterface
    {
        return $this->setData(ConsentLogInterface::CREATED_AT, $gmtDateTime);
    }
}
