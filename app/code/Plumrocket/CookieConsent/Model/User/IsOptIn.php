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

namespace Plumrocket\CookieConsent\Model\User;

use Magento\Framework\Exception\NotFoundException;
use Plumrocket\CookieConsent\Api\GetUserConsentInterface;
use Plumrocket\CookieConsent\Api\IsUserOptInInterface;

/**
 * @since 1.0.0
 */
class IsOptIn implements IsUserOptInInterface
{
    /**
     * @var \Plumrocket\CookieConsent\Api\GetUserConsentInterface
     */
    private $getUserConsent;

    /**
     * @param \Plumrocket\CookieConsent\Api\GetUserConsentInterface $getUserConsent
     */
    public function __construct(GetUserConsentInterface $getUserConsent)
    {
        $this->getUserConsent = $getUserConsent;
    }

    /**
     * @inheritDoc
     */
    public function execute(): bool
    {
        try {
            $this->getUserConsent->execute();
            return true;
        } catch (NotFoundException $e) {
            return false;
        }
    }
}
