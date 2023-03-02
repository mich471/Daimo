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

namespace Plumrocket\CookieConsent\Model\Category;

use Plumrocket\CookieConsent\Api\CanManageCookieInterface;
use Plumrocket\CookieConsent\Api\Data\CategoryInterface;
use Plumrocket\CookieConsent\Api\GetEssentialCategoryKeysInterface;
use Plumrocket\CookieConsent\Api\GetUserConsentInterface;
use Plumrocket\CookieConsent\Api\IsAllowedCategoryInterface;
use Plumrocket\CookieConsent\Api\IsUserOptInInterface;
use Plumrocket\CookieConsent\Helper\Config;

/**
 * @since 1.0.0
 */
class IsAllowed implements IsAllowedCategoryInterface
{
    /**
     * @var \Plumrocket\CookieConsent\Api\CanManageCookieInterface
     */
    private $canManageCookie;

    /**
     * @var \Plumrocket\CookieConsent\Api\GetUserConsentInterface
     */
    private $getUserConsent;

    /**
     * @var \Plumrocket\CookieConsent\Api\IsUserOptInInterface
     */
    private $isUserOptIn;

    /**
     * @var \Plumrocket\CookieConsent\Helper\Config
     */
    private $config;

    /**
     * @var \Plumrocket\CookieConsent\Api\GetEssentialCategoryKeysInterface
     */
    private $getEssentialCategoryKeys;

    /**
     * @param \Plumrocket\CookieConsent\Api\CanManageCookieInterface          $canManageCookie
     * @param \Plumrocket\CookieConsent\Api\GetUserConsentInterface           $getUserConsent
     * @param \Plumrocket\CookieConsent\Api\IsUserOptInInterface              $isUserOptIn
     * @param \Plumrocket\CookieConsent\Helper\Config                         $config
     * @param \Plumrocket\CookieConsent\Api\GetEssentialCategoryKeysInterface $getEssentialCategoryKeys
     */
    public function __construct(
        CanManageCookieInterface $canManageCookie,
        GetUserConsentInterface $getUserConsent,
        IsUserOptInInterface $isUserOptIn,
        Config $config,
        GetEssentialCategoryKeysInterface $getEssentialCategoryKeys
    ) {
        $this->canManageCookie = $canManageCookie;
        $this->getUserConsent = $getUserConsent;
        $this->isUserOptIn = $isUserOptIn;
        $this->config = $config;
        $this->getEssentialCategoryKeys = $getEssentialCategoryKeys;
    }

    /**
     * This logic has js alternative in CookieRestriction.isAllowedCategory
     *
     * @inheritDoc
     */
    public function execute(string $key): bool
    {
        if (! $this->canManageCookie->execute()) {
            return true;
        }

        if (in_array($key, $this->getEssentialCategoryKeys->execute(), true)) {
            return true;
        }

        if ($this->isUserOptIn->execute()) {
            $allowedCategories = $this->getUserConsent->execute();
            if (in_array(CategoryInterface::ALL_CATEGORIES, $allowedCategories, true)) {
                return true;
            }

            return in_array($key, $allowedCategories, true);
        }

        return $this->config->canUseCookieBeforeOptIn();
    }
}
