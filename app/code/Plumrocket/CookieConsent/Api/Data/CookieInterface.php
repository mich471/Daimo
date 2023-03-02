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

namespace Plumrocket\CookieConsent\Api\Data;

/**
 * @since 1.0.0
 */
interface CookieInterface
{
    public const DATA_PERSISTOR_KEY = 'pr-cookie-item';

    public const NAME = 'name';
    public const CATEGORY_KEY = 'category_key';
    public const DESCRIPTION = 'description';
    public const DOMAIN = 'domain';
    public const DURATION = 'duration';
    public const TYPE = 'type';

    /**
     * Get cookie name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set cookie name.
     *
     * @param string $name
     * @return \Plumrocket\CookieConsent\Api\Data\CookieInterface
     */
    public function setName(string $name): CookieInterface;

    /**
     * Get category key.
     *
     * @return string
     */
    public function getCategoryKey(): string;

    /**
     * Set category key.
     *
     * @param string $categoryKey
     * @return \Plumrocket\CookieConsent\Api\Data\CookieInterface
     */
    public function setCategoryKey(string $categoryKey): CookieInterface;

    /**
     * @return bool
     */
    public function isFirstParty(): bool;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $type
     * @return \Plumrocket\CookieConsent\Api\Data\CookieInterface
     */
    public function setType(string $type): CookieInterface;

    /**
     * @return string
     */
    public function getDomain(): string;

    /**
     * @param string $domain
     * @return \Plumrocket\CookieConsent\Api\Data\CookieInterface
     */
    public function setDomain(string $domain): CookieInterface;

    /**
     * @return int
     */
    public function getDuration(): int;

    /**
     * @param int $duration
     * @return \Plumrocket\CookieConsent\Api\Data\CookieInterface
     */
    public function setDuration(int $duration): CookieInterface;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @param string $description
     * @return \Plumrocket\CookieConsent\Api\Data\CookieInterface
     */
    public function setDescription(string $description): CookieInterface;
}
