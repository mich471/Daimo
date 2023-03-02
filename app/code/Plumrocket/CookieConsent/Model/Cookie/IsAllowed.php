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

namespace Plumrocket\CookieConsent\Model\Cookie;

use Plumrocket\CookieConsent\Api\CanManageCookieInterface;
use Plumrocket\CookieConsent\Api\IsAllowedCategoryInterface;
use Plumrocket\CookieConsent\Api\IsAllowedCookieInterface;
use Plumrocket\CookieConsent\Api\IsUserOptInInterface;
use Plumrocket\CookieConsent\Helper\Config;
use Plumrocket\CookieConsent\Model\Cookie\Name\GetTrueName;
use Plumrocket\CookieConsent\Model\ResourceModel\Cookie\GetCategoryKey;
use Plumrocket\CookieConsent\Model\ResourceModel\Cookie\IsKnown;

/**
 * @since 1.0.0
 */
class IsAllowed implements IsAllowedCookieInterface
{
    /**
     * System cookie always allowed
     *
     * @var array
     */
    private $systemCookies;

    /**
     * @var \Plumrocket\CookieConsent\Api\CanManageCookieInterface
     */
    private $canManageCookie;

    /**
     * @var \Plumrocket\CookieConsent\Helper\Config
     */
    private $config;

    /**
     * @var \Plumrocket\CookieConsent\Model\ResourceModel\Cookie\GetCategoryKey
     */
    private $getCategoryKey;

    /**
     * @var \Plumrocket\CookieConsent\Api\IsAllowedCategoryInterface
     */
    private $isAllowedCategory;

    /**
     * @var \Plumrocket\CookieConsent\Model\ResourceModel\Cookie\IsKnown
     */
    private $isKnownCookie;

    /**
     * @var \Plumrocket\CookieConsent\Api\IsUserOptInInterface
     */
    private $isUserOptIn;

    /**
     * @var \Plumrocket\CookieConsent\Model\Cookie\Name\GetTrueName
     */
    private $getTrueName;

    /**
     * @param \Plumrocket\CookieConsent\Api\CanManageCookieInterface              $canManageCookie
     * @param \Plumrocket\CookieConsent\Helper\Config                             $config
     * @param \Plumrocket\CookieConsent\Api\IsUserOptInInterface                  $isUserOptIn
     * @param \Plumrocket\CookieConsent\Model\ResourceModel\Cookie\GetCategoryKey $getCategoryKey
     * @param \Plumrocket\CookieConsent\Api\IsAllowedCategoryInterface            $isAllowedCategory
     * @param \Plumrocket\CookieConsent\Model\ResourceModel\Cookie\IsKnown        $isKnownCookie
     * @param \Plumrocket\CookieConsent\Model\Cookie\Name\GetTrueName             $getTrueName
     * @param array                                                               $systemCookies
     */
    public function __construct(
        CanManageCookieInterface $canManageCookie,
        Config $config,
        IsUserOptInInterface $isUserOptIn,
        GetCategoryKey $getCategoryKey,
        IsAllowedCategoryInterface $isAllowedCategory,
        IsKnown $isKnownCookie,
        GetTrueName $getTrueName,
        array $systemCookies = []
    ) {
        $this->canManageCookie = $canManageCookie;
        $this->config = $config;
        $this->isUserOptIn = $isUserOptIn;
        $this->getCategoryKey = $getCategoryKey;
        $this->isAllowedCategory = $isAllowedCategory;
        $this->isKnownCookie = $isKnownCookie;
        $this->systemCookies = $systemCookies;
        $this->getTrueName = $getTrueName;
    }

    /**
     * This logic has js alternative in CookieRestriction.isAllowed
     *
     * @inheritDoc
     */
    public function execute(string $name): bool
    {
        if (in_array($name, $this->systemCookies, true)) {
            return true;
        }

        if (! $this->canManageCookie->execute()) {
            return true;
        }

        $name = $this->getTrueName->execute($name);

        if (! $this->isUserOptIn->execute()) {
            if ($this->config->canUseCookieBeforeOptIn()) {
                return true;
            }

            if ($this->isKnownCookie->execute($name)) {
                return $this->isAllowedCategory->execute($this->getCategoryKey->execute($name));
            }

            return false;
        }

        if (! $this->isKnownCookie->execute($name)) {
            return ! $this->config->canBlockUnknownCookie();
        }

        return $this->isAllowedCategory->execute($this->getCategoryKey->execute($name));
    }
}
