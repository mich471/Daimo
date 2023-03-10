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

namespace Plumrocket\CookieConsent\Ui\Locator;

use Plumrocket\CookieConsent\Api\Data\CookieInterface;

/**
 * @since 1.0.0
 */
class CookieContainerLocator extends AbstractContainerLocator implements CookieLocatorInterface
{
    /**
     * @inheritDoc
     */
    public function getCookie(): CookieInterface
    {
        return $this->getModel();
    }

    /**
     * @inheritDoc
     */
    public function setCookie(CookieInterface $cookie): CookieLocatorInterface
    {
        $this->setModel($cookie);
        return $this;
    }
}
