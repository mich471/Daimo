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

namespace Plumrocket\CookieConsent\Ui\Locator;

use Plumrocket\CookieConsent\Api\Data\CookieInterface;

/**
 * @since 1.0.0
 */
interface CookieLocatorInterface extends LocatorInterface
{
    /**
     * @return \Plumrocket\CookieConsent\Api\Data\CookieInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getCookie(): CookieInterface;

    /**
     * @param \Plumrocket\CookieConsent\Api\Data\CookieInterface $category
     * @return \Plumrocket\CookieConsent\Ui\Locator\CookieLocatorInterface
     */
    public function setCookie(CookieInterface $category): CookieLocatorInterface;
}
