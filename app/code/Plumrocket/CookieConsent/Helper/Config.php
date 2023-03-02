<?php
/**
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * @since 1.0.0
 */
class Config extends AbstractHelper
{

    public const XML_PATH_IS_ENABLED = 'pr_cookie/general/enabled';
    public const XML_PATH_USE_COOKIE_BEFORE_OPT_IN = 'pr_cookie/main_settings/use_cookie_before_opt_in';
    public const XML_PATH_BLOCK_UNKNOWN_COOKIE = 'pr_cookie/main_settings/block_unknown_cookie';
    public const XML_PATH_CONSENT_EXPIRY = 'pr_cookie/main_settings/consent_expiry';
    public const XML_PATH_RELOAD_AFTER_ACCEPT = 'pr_cookie/main_settings/reload_after_accept';
    public const XML_PATH_RELOAD_AFTER_DECLINE = 'pr_cookie/main_settings/reload_after_decline';

    public const XML_PATH_GTM_ENABLED = 'prgdpr/gtm/enabled';
    public const XML_PATH_GTM_CATEGORY_KEY = 'pr_cookie/gtm/category';
    public const XML_PATH_GTM_CONTAINER_ID = 'prgdpr/gtm/container_id';

    /**
     * @var \Plumrocket\Base\Model\Utils\Config
     */
    private $configUtils;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Plumrocket\Base\Model\Utils\Config   $configUtils
     */
    public function __construct(
        Context $context,
        \Plumrocket\Base\Model\Utils\Config $configUtils
    ) {
        parent::__construct($context);
        $this->configUtils = $configUtils;
    }

    /**
     * Receive magento config value
     *
     * @param string      $path      full path, eg: "pr_base/general/enabled"
     * @param string|int  $scopeCode store view code or website code
     * @param string|null $scopeType
     * @return mixed
     */
    public function getConfig($path, $scopeCode = null, $scopeType = null)
    {
        if ($scopeType === null) {
            $scopeType = ScopeInterface::SCOPE_STORE;
        }
        return $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
    }

    /**
     * Check if module is enabled.
     *
     * @param null $store
     * @param null $scope
     * @return bool
     */
    public function isModuleEnabled($store = null, $scope = null): bool
    {
        return $this->configUtils->isSetFlag(
            self::XML_PATH_IS_ENABLED,
            $store,
            $scope
        );
    }

    /**
     * @param null $store
     * @param null $scope
     * @return bool
     */
    public function canUseCookieBeforeOptIn($store = null, $scope = null): bool
    {
        return $this->configUtils->isSetFlag(
            self::XML_PATH_USE_COOKIE_BEFORE_OPT_IN,
            $store,
            $scope
        );
    }

    /**
     * @param null $store
     * @param null $scope
     * @return bool
     */
    public function canBlockUnknownCookie($store = null, $scope = null): bool
    {
        return $this->configUtils->isSetFlag(
            self::XML_PATH_BLOCK_UNKNOWN_COOKIE,
            $store,
            $scope
        );
    }

    /**
     * @param null $store
     * @param null $scope
     * @return int
     */
    public function getConsentExpiry($store = null, $scope = null): int
    {
        return (int) $this->configUtils->getConfig(self::XML_PATH_CONSENT_EXPIRY, $store, $scope);
    }

    /**
     * @param null $store
     * @param null $scope
     * @return bool
     */
    public function reloadAfterAccept($store = null, $scope = null): bool
    {
        return $this->configUtils->isSetFlag(self::XML_PATH_RELOAD_AFTER_ACCEPT, $store, $scope);
    }

    /**
     * @param null $store
     * @param null $scope
     * @return bool
     */
    public function reloadAfterDecline($store = null, $scope = null): bool
    {
        return $this->configUtils->isSetFlag(self::XML_PATH_RELOAD_AFTER_DECLINE, $store, $scope);
    }

    /** ------ GMT ------- */

    /**
     * @param null $store
     * @param null $scope
     * @return bool
     */
    public function isGtmEnabled($store = null, $scope = null): bool
    {
        return $this->configUtils->isSetFlag(self::XML_PATH_GTM_ENABLED, $store, $scope);
    }

    /**
     * @param null $store
     * @param null $scope
     * @return string
     */
    public function getGmtAssociatedCategoryKey($store = null, $scope = null): string
    {
        return (string) $this->configUtils->getConfig(self::XML_PATH_GTM_CATEGORY_KEY, $store, $scope);
    }

    /**
     * @param null $store
     * @param null $scope
     * @return string
     */
    public function getGtmContainerId($store = null, $scope = null): string
    {
        return (string) $this->configUtils->getConfig(self::XML_PATH_GTM_CONTAINER_ID, $store, $scope);
    }
}
