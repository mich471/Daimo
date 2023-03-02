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

namespace Plumrocket\CookieConsent\Model\Cookie;

use Plumrocket\CookieConsent\Api\Data\CookieInterface;

/**
 * @since 1.0.0
 */
class Duration
{
    const ONE_YEAR = 31536000;
    const TWO_YEARS = 63072000;
    const SESSION = 0;
    const ONE_DAY = 86400;
    const FOR_LOCAL_STORAGE_COOKIE = 86400;
    const ONE_HOUR = 3600;

    /**
     * @param \Plumrocket\CookieConsent\Api\Data\CookieInterface $cookie
     * @return string
     */
    public function getLabel(CookieInterface $cookie): string
    {
        return $this->durationToLabel($cookie->getDuration());
    }

    /**
     * @param int $seconds
     * @return string
     */
    public function durationToLabel(int $seconds): string
    {
        if (self::SESSION === $seconds) {
            return (string) __('Session');
        }

        if ($seconds >= self::ONE_DAY) {
            $days = round($seconds / self::ONE_DAY);
            if ($days > 1) {
                return (string) __('%1 days', $days);
            }

            return (string) __('1 day');
        }

        if ($seconds >= self::ONE_HOUR) {
            $hours = round($seconds / self::ONE_HOUR);
            if ($hours > 1) {
                return (string) __('%1 hours', $hours);
            }

            return (string) __('1 hour');
        }

        return (string) __('less than an hour');
    }
}
