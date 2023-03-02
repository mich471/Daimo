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

use Magento\Framework\Session\Config\ConfigInterface;
use Plumrocket\CookieConsent\Api\Data\CookieInterface;

/**
 * @since 1.0.0
 */
class Domain
{
    /**
     * @var \Magento\Framework\Session\Config\ConfigInterface
     */
    private $sessionConfig;

    /**
     * @param \Magento\Framework\Session\Config\ConfigInterface $sessionConfig
     */
    public function __construct(ConfigInterface $sessionConfig)
    {
        $this->sessionConfig = $sessionConfig;
    }

    /**
     * @param \Plumrocket\CookieConsent\Api\Data\CookieInterface $cookie
     * @return string
     */
    public function getLabel(CookieInterface $cookie): string
    {
        if ($cookie->isFirstParty() && ! $cookie->getDomain()) {
            return $this->sessionConfig->getCookieDomain();
        }

        return $cookie->getDomain();
    }
}
