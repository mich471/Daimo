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
interface CategoryInterface
{
    public const DATA_PERSISTOR_KEY = 'pr-cookie-category';
    public const ALL_CATEGORIES = 'all';

    public const STATUS = 'status';
    public const IS_PRE_CHECKED = 'is_pre_checked';
    public const IS_ESSENTIAL = 'is_essential';
    public const KEY = 'key';
    public const NAME = 'name';
    public const DESCRIPTION = 'description';
    public const SORT_ORDER = 'sort_order';
    public const HEAD_SCRIPTS = 'head_scripts';
    public const FOOTER_MISCELLANEOUS_HTML = 'footer_miscellaneous_html';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

    /**
     * Set id.
     *
     * @param int $value
     * @return \Plumrocket\CookieConsent\Api\Data\CategoryInterface
     */
    public function setId($value);

    /**
     * Check if category is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Set status.
     *
     * @param bool $status
     * @return \Plumrocket\CookieConsent\Api\Data\CategoryInterface
     */
    public function setStatus(bool $status): CategoryInterface;

    /**
     * Defined if category is enabled by default in settings panel.
     *
     * If category is "pre-checked" it means that when customer open settings panel in first time,
     * this category will be checked as enabled but its only view.
     * This feature was being added for encourage customer to accept more cookie categories.
     *
     * @return bool
     */
    public function isPreChecked(): bool;

    /**
     * @param bool $flag
     * @return \Plumrocket\CookieConsent\Api\Data\CategoryInterface
     */
    public function setIsPreChecked(bool $flag): CategoryInterface;

    /**
     * @return bool
     */
    public function isEssential(): bool;

    /**
     * @param bool $isEssential
     * @return \Plumrocket\CookieConsent\Api\Data\CategoryInterface
     */
    public function setIsEssential(bool $isEssential): CategoryInterface;

    /**
     * @return string
     */
    public function getKey(): string;

    /**
     * @param string $key
     * @return \Plumrocket\CookieConsent\Api\Data\CategoryInterface
     */
    public function setKey(string $key): CategoryInterface;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     * @return \Plumrocket\CookieConsent\Api\Data\CategoryInterface
     */
    public function setName(string $name): CategoryInterface;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @param string $description
     * @return \Plumrocket\CookieConsent\Api\Data\CategoryInterface
     */
    public function setDescription(string $description): CategoryInterface;

    /**
     * @return int
     */
    public function getSortOrder(): int;

    /**
     * @param int $sortOrder
     * @return \Plumrocket\CookieConsent\Api\Data\CategoryInterface
     */
    public function setSortOrder(int $sortOrder): CategoryInterface;

    /**
     * @return string
     */
    public function getHeadScripts(): string;

    /**
     * @param string $headScripts
     * @return \Plumrocket\CookieConsent\Api\Data\CategoryInterface
     */
    public function setHeadScripts(string $headScripts): CategoryInterface;

    /**
     * @return string
     */
    public function getFooterMiscellaneousHtml(): string;

    /**
     * @param string $footerMiscellaneousHtml
     * @return \Plumrocket\CookieConsent\Api\Data\CategoryInterface
     */
    public function setFooterMiscellaneousHtml(string $footerMiscellaneousHtml): CategoryInterface;
}
