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

namespace Plumrocket\CookieConsent\Block;

use Magento\Cookie\Helper\Cookie as CookieHelper;
use Magento\Customer\Model\Context as HttpContext;
use Magento\Customer\Model\GroupManagement;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Session\Config\ConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Plumrocket\CookieConsent\Api\CanManageCookieInterface;
use Plumrocket\CookieConsent\Api\GetCookieToCategoryMappingInterface;
use Plumrocket\CookieConsent\Api\GetEssentialCategoryKeysInterface;
use Plumrocket\CookieConsent\Helper\Config;
use Plumrocket\CookieConsent\Model\Cookie\Name\ToRegex;
use Plumrocket\CookieConsent\Model\ResourceModel\Cookie\GetDynamicNames;

/**
 * @since 1.0.0
 */
class RestrictionConfig extends Template
{
    /**
     * @var \Plumrocket\CookieConsent\Helper\Config
     */
    private $config;

    /**
     * @var \Plumrocket\CookieConsent\Api\GetEssentialCategoryKeysInterface
     */
    private $getEssentialCategoryKeys;

    /**
     * @var \Plumrocket\CookieConsent\Api\GetCookieToCategoryMappingInterface
     */
    private $getCookieToCategoryMapping;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Plumrocket\CookieConsent\Api\CanManageCookieInterface
     */
    private $canManageCookie;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private $httpContext;

    /**
     * @var \Magento\Framework\Session\Config\ConfigInterface
     */
    private $cookieConfig;

    /**
     * @var \Magento\Cookie\Helper\Cookie
     */
    private $cookieConfigHelper;

    /**
     * @var \Plumrocket\CookieConsent\Model\ResourceModel\Cookie\GetDynamicNames
     */
    private $getDynamicNames;

    /**
     * @var \Plumrocket\CookieConsent\Model\Cookie\Name\ToRegex
     */
    private $toRegex;

    /**
     * Mapping constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context                     $context
     * @param \Plumrocket\CookieConsent\Helper\Config                              $config
     * @param \Plumrocket\CookieConsent\Api\GetEssentialCategoryKeysInterface      $getEssentialCategoryKeys
     * @param \Plumrocket\CookieConsent\Api\GetCookieToCategoryMappingInterface    $getCookieToCategoryMapping
     * @param \Magento\Framework\Serialize\SerializerInterface                     $serializer
     * @param \Plumrocket\CookieConsent\Api\CanManageCookieInterface               $canManageCookie
     * @param \Magento\Framework\App\Http\Context                                  $httpContext
     * @param \Magento\Framework\Session\Config\ConfigInterface                    $cookieConfig
     * @param \Magento\Cookie\Helper\Cookie                                        $cookieConfigHelper
     * @param \Plumrocket\CookieConsent\Model\ResourceModel\Cookie\GetDynamicNames $getDynamicNames
     * @param \Plumrocket\CookieConsent\Model\Cookie\Name\ToRegex                  $toRegex
     * @param array                                                                $data
     */
    public function __construct(
        Context $context,
        Config $config,
        GetEssentialCategoryKeysInterface $getEssentialCategoryKeys,
        GetCookieToCategoryMappingInterface $getCookieToCategoryMapping,
        SerializerInterface $serializer,
        CanManageCookieInterface $canManageCookie,
        \Magento\Framework\App\Http\Context $httpContext,
        ConfigInterface $cookieConfig,
        CookieHelper $cookieConfigHelper,
        GetDynamicNames $getDynamicNames,
        ToRegex $toRegex,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->serializer = $serializer;
        $this->getEssentialCategoryKeys = $getEssentialCategoryKeys;
        $this->getCookieToCategoryMapping = $getCookieToCategoryMapping;
        $this->canManageCookie = $canManageCookie;
        $this->httpContext = $httpContext;
        $this->cookieConfig = $cookieConfig;
        $this->cookieConfigHelper = $cookieConfigHelper;
        $this->getDynamicNames = $getDynamicNames;
        $this->toRegex = $toRegex;
    }

    /**
     * @return array
     */
    public function getRestrictionConfig(): array
    {
        return [
            'canManageCookie' => $this->canManageCookie->execute(),
            'canUseCookieBeforeOptIn' => $this->config->canUseCookieBeforeOptIn(),
            'canBlockUnknownCookie' => $this->config->canBlockUnknownCookie(),
            'consent' => [
                'isLoggedIn'         => $this->isLoggedIn(),
                'logUrl' => $this->isLoggedIn()
                    ? $this->getCustomerConsentUpdateUrl()
                    : $this->getGuestConsentUpdateUrl(),
                'reloadAfterAccept'  => $this->config->reloadAfterAccept(),
                'reloadAfterDecline' => $this->config->reloadAfterDecline(),
                'expiry'             => $this->config->getConsentExpiry(),
            ],
            'cookie' => [
                'path' => $this->cookieConfig->getCookiePath(),
                'domain' => $this->cookieConfig->getCookieDomain(),
            ],
            'mage' => [
                'website' => (int) $this->_storeManager->getWebsite()->getId(),
                'cookieName' => CookieHelper::IS_USER_ALLOWED_SAVE_COOKIE,
                'lifetime' => $this->cookieConfigHelper->getCookieRestrictionLifetime(),
            ],
            'cookieToCategoryMapping' => $this->getCookieToCategoryMapping->execute(),
            'essentialCategoryKeys' => $this->getEssentialCategoryKeys->execute(),
            'dynamicNamesPatterns' => $this->toRegex->execute($this->getDynamicNames->execute()),
        ];
    }

    /**
     * @return string
     */
    public function getSerializedRestrictionConfig(): string
    {
        return $this->serializer->serialize($this->getRestrictionConfig());
    }

    /**
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return GroupManagement::NOT_LOGGED_IN_ID !== $this->httpContext->getValue(HttpContext::CONTEXT_GROUP);
    }

    /**
     * @return string
     */
    public function getCustomerConsentUpdateUrl(): string
    {
        return $this->getUrl('pr_cookie/consent_customer/update');
    }

    /**
     * @return string
     */
    public function getGuestConsentUpdateUrl(): string
    {
        return $this->getUrl('pr_cookie/consent_guest/update');
    }
}
