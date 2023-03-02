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
 * @package     Plumrocket_magento2.3.5
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Model\ResourceModel\Cookie;

/**
 * Retrieve if there's cookie with requested name in the database
 *
 * @since 1.0.0
 */
class IsKnown
{
    /**
     * @var \Plumrocket\CookieConsent\Model\ResourceModel\Cookie\GetCategoryKey
     */
    private $getCookieCategoryKey;

    /**
     * @param \Plumrocket\CookieConsent\Model\ResourceModel\Cookie\GetCategoryKey $getCategoryKey
     */
    public function __construct(GetCategoryKey $getCategoryKey)
    {
        $this->getCookieCategoryKey = $getCategoryKey;
    }

    /**
     * @param string $name
     * @param bool   $forceReload
     * @return bool
     */
    public function execute(string $name, bool $forceReload = false): bool
    {
        /**
         * As category_key is required field for cookie, if GetCategoryKey returns '' it means that cookie unknown
         * So, we optimize request count to database, because GetCategoryKey has local cache
         */
        return (bool) $this->getCookieCategoryKey->execute($name, $forceReload);
    }
}
