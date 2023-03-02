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

namespace Plumrocket\CookieConsent\Plugin\Cookie;

use Magento\Cookie\Helper\Cookie;
use Plumrocket\CookieConsent\Api\CanManageCookieInterface;
use Plumrocket\CookieConsent\Helper\Config;

/**
 * @since 1.0.0
 */
class RestrictionMode
{
    /**
     * @var \Plumrocket\CookieConsent\Api\CanManageCookieInterface
     */
    private $canManageCookie;

    /**
     * @var \Plumrocket\CookieConsent\Helper\Config
     */
    private $config;

    /**
     * RestrictionMode constructor.
     *
     * @param \Plumrocket\CookieConsent\Api\CanManageCookieInterface $canManageCookie
     * @param \Plumrocket\CookieConsent\Helper\Config                $config
     */
    public function __construct(
        CanManageCookieInterface $canManageCookie,
        Config $config
    ) {
        $this->canManageCookie = $canManageCookie;
        $this->config = $config;
    }

    /**
     * @param \Magento\Cookie\Helper\Cookie $subject
     * @param                               $result
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return bool
     */
    public function afterIsCookieRestrictionModeEnabled(Cookie $subject, $result)
    {
        if ($this->config->isModuleEnabled()) {
            return $this->canManageCookie->execute();
        }

        return $result;
    }
}
