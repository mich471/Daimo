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

namespace Plumrocket\CookieConsent\Model\Cookie\Name;

/**
 * Convert dynamic names to regex without borders /../
 *
 * @since 1.1.1
 */
class ToRegex
{
    /**
     * @param string[] $names
     * @return string[]
     */
    public function execute(array $names): array
    {
        $result = [];
        foreach ($names as $name) {
            $result[$name] = str_replace('*', '.*', $name);
        }

        return $result;
    }
}
