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

use Plumrocket\CookieConsent\Model\ResourceModel\Cookie\GetDynamicNames;

/**
 * If cookie name match some pattern if would be replaced to name in data base
 *
 * @since 1.1.1
 */
class GetTrueName
{
    /**
     * @var \Plumrocket\CookieConsent\Model\ResourceModel\Cookie\GetDynamicNames
     */
    private $getDynamicNames;

    /**
     * @var \Plumrocket\CookieConsent\Model\Cookie\Name\ToRegex
     */
    private $toRegex;

    /**
     * @param \Plumrocket\CookieConsent\Model\ResourceModel\Cookie\GetDynamicNames $getDynamicNames
     * @param \Plumrocket\CookieConsent\Model\Cookie\Name\ToRegex                  $toRegex
     */
    public function __construct(
        GetDynamicNames $getDynamicNames,
        ToRegex $toRegex
    ) {
        $this->getDynamicNames = $getDynamicNames;
        $this->toRegex = $toRegex;
    }

    public function execute(string $name): string
    {
        $patterns = $this->toRegex->execute($this->getDynamicNames->execute());

        foreach ($patterns as $dynamicName => $pattern) {
            if (preg_match("#$pattern#", $name)) {
                return $dynamicName;
            }
        }

        return $name;
    }
}
