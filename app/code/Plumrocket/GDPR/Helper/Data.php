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
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Helper;

use Magento\Cms\Model\Template\FilterProvider;
use Plumrocket\DataPrivacy\Helper\Config\PrivacyCenterDashboard;
use Plumrocket\GDPR\Model\Base\Information;

/**
 * @since 1.0.0
 * @deprecated since 3.2.0
 */
class Data extends Main
{
    const SECTION_ID = 'prgdpr';
    const LAST_ALLOW_COOKIE_NAME = 'allowed_cookies_datetime';
    const LAST_DECLINE_COOKIE_NAME = 'declined_cookies_datetime';
    const IS_USER_DECLINE_SAVE_COOKIE = 'user_decline_save_cookie';

    /**
     * @var string
     */
    protected $_configSectionId = Information::CONFIG_SECTION;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Cms\Api\PageRepositoryInterface
     */
    private $pageRepositoryInterface;

    /**
     * @var FilterProvider
     */
    private $filterProvider;

    /**
     * @var \Plumrocket\DataPrivacy\Helper\Config
     */
    private $config;

    /**
     * @var \Plumrocket\DataPrivacy\Helper\Config\PrivacyCenterDashboard
     */
    private $privacyCenterDashboardConfig;

    /**
     * @param \Magento\Framework\ObjectManagerInterface                    $objectManager
     * @param \Magento\Framework\App\Helper\Context                        $context
     * @param \Magento\Cms\Api\PageRepositoryInterface                     $pageRepositoryInterface
     * @param \Magento\Cms\Model\Template\FilterProvider                   $filterProvider
     * @param \Plumrocket\DataPrivacy\Helper\Config                        $config
     * @param \Plumrocket\DataPrivacy\Helper\Config\PrivacyCenterDashboard $privacyCenterDashboardConfig
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepositoryInterface,
        FilterProvider $filterProvider,
        \Plumrocket\DataPrivacy\Helper\Config $config,
        PrivacyCenterDashboard $privacyCenterDashboardConfig
    ) {
        parent::__construct($objectManager, $context);
        $this->urlBuilder                   = $context->getUrlBuilder();
        $this->pageRepositoryInterface      = $pageRepositoryInterface;
        $this->filterProvider               = $filterProvider;
        $this->config                       = $config;
        $this->privacyCenterDashboardConfig = $privacyCenterDashboardConfig;
    }

    /**
     * Is module enabled
     *
     * @deprecated since 3.0.0
     * @see \Plumrocket\DataPrivacy\Helper\Config::isModuleEnabled()
     * @param null $store
     * @return bool
     */
    public function moduleEnabled($store = null)
    {
        return $this->config->isModuleEnabled($store);
    }

    /**
     * @deprecated since 3.0.0
     * @see \Plumrocket\DataPrivacy\Helper\Config::isAccountExportEnabled()
     *
     * @param null $store
     * @return bool
     */
    public function isAccountExportEnabled($store = null)
    {
        return $this->config->isAccountExportEnabled($store);
    }

    /**
     * @deprecated since 3.0.0
     * @see \Plumrocket\DataPrivacy\Helper\Config::isAccountDeletionEnabled()
     *
     * @param null $store
     * @return bool
     */
    public function isAccountDeletionEnabled($store = null)
    {
        return $this->config->isAccountDeletionEnabled($store);
    }

    /**
     * @param int|string $store
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @deprecated since 3.2.0
     */
    public function getPrivacyPolicyPageUrl($store = null)
    {
        $pageUrl = '';

        if ($pageId = $this->privacyCenterDashboardConfig->getPrivacyPolicyPageId($store)) {
            $page = $this->pageRepositoryInterface->getById($pageId);

            if ($page) {
                $pageUrl = $this->urlBuilder->getUrl($page->getIdentifier());
            }
        }

        return $pageUrl;
    }

    /**
     * @deprecated since 3.0.0
     * @see \Plumrocket\DataPrivacy\Helper\Config\PrivacyCenterDashboard::getPrivacyPolicyPageId()
     *
     * @param null $store
     * @return mixed
     */
    public function getPrivacyPolicyPageId($store = null)
    {
        return $this->privacyCenterDashboardConfig->getPrivacyPolicyPageId($store);
    }

    /**
     * @param int|string $store
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @deprecated since 3.2.0
     */
    public function getCookiePolicyPageUrl($store = null)
    {
        $pageUrl = '';

        if ($pageId = $this->privacyCenterDashboardConfig->getCookiePolicyPageId($store)) {
            $page = $this->pageRepositoryInterface->getById($pageId);

            if ($page) {
                $pageUrl = $this->urlBuilder->getUrl($page->getIdentifier());
            }
        }

        return $pageUrl;
    }

    /**
     * @deprecated since 3.0.0
     * @see \Plumrocket\DataPrivacy\Helper\Config\PrivacyCenterDashboard::getCookiePolicyPageId()
     *
     * @param null $store
     * @return mixed
     */
    public function getCookiePolicyPageId($store = null)
    {
        return $this->privacyCenterDashboardConfig->getCookiePolicyPageId($store);
    }

    /**
     * @param int|string $store
     * @return string
     * @deprecated since 3.2.0
     */
    public function getProtectionOfficerEmail($store = null)
    {
        return $this->getConfig($this->_configSectionId . '/dashboard/protection_officer_email', $store);
    }

    /**
     * @param int|string $store
     * @return string
     * @deprecated since 3.2.0
     */
    public function isGtmEnabled($store = null)
    {
        return $this->getConfig($this->_configSectionId . '/gtm/enabled', $store);
    }

    /**
     * @param int|string $store
     * @return string
     * @deprecated since 3.2.0
     */
    public function getGtmContainerId($store = null)
    {
        return $this->getConfig($this->_configSectionId . '/gtm/container_id', $store);
    }

    /**
     * @param int|string $store
     * @return string
     * @deprecated since 3.2.0
     */
    public function getEmailSenderName($store = null)
    {
        return $this->getConfig($this->_configSectionId . '/email/sender_name', $store);
    }

    /**
     * @param int|string $store
     * @return string
     * @deprecated since 3.2.0
     */
    public function getEmailSenderEmail($store = null)
    {
        return $this->getConfig($this->_configSectionId . '/email/sender_email', $store);
    }

    /**
     * @param int|string $store
     * @return string
     * @deprecated since 3.2.0
     */
    public function getEmailDownloadConfirmationTemplate($store = null)
    {
        return $this->getConfig($this->_configSectionId . '/email/download_confirmation_template', $store);
    }

    /**
     * @param int|string $store
     * @return string
     * @deprecated since 3.2.0
     */
    public function getEmailRemovalRequestTemplate($store = null)
    {
        return $this->getConfig($this->_configSectionId . '/email/removal_request_template', $store);
    }

    /**
     * @return string
     * @deprecated since 3.2.0
     */
    public function getConfigSectionId()
    {
        return $this->_configSectionId;
    }

    /**
     * @deprecated since 3.0.0
     * @see \Plumrocket\DataPrivacy\Helper\Config::getDeletionTime()
     *
     * @return int
     */
    public function getDeletionTime()
    {
        return $this->config->getDeletionTime();
    }

    /**
     * @param null $store
     * @return string
     * @deprecated since 3.2.0
     * @see \Plumrocket\DataPrivacy\Model\Account\Data\Anonymizer::getKey
     */
    public function getAnonymizationKey($store = null)
    {
        $key = trim($this->getConfig($this->_configSectionId . '/removal_settings/anonymization_key', $store));

        if (! $key) {
            $key = 'xxxx';
        }

        return $key;
    }

    /**
     * @param $name
     * @return mixed
     * @deprecated since 3.2.0
     */
    public function getResourceByName($name)
    {
        try {
            $object = $this->_objectManager->create($name);
        } catch (\Exception $e) {
            // Exception, do nothing
            $object = null;
        }

        return $object;
    }

    /**
     * @param string $token
     * @return string
     * @deprecated since 3.2.0
     */
    public function getGuestPrivacyCenterUrl(string $token)
    {
        return $this->urlBuilder->getUrl('prgdpr/account/index', ['token' => $token]);
    }

    /**
     * @deprecated since 3.0.0
     * @see \Plumrocket\DataPrivacy\Helper\Config\PrivacyCenterDashboard::getGuestLinkExpiration()
     *
     * @param null $store
     * @return mixed
     */
    public function getGuestExpirationLink($store = null)
    {
        return $this->privacyCenterDashboardConfig->getGuestLinkExpiration($store);
    }

    /**
     * @param null $store
     * @return mixed
     * @deprecated since 3.2.0
     */
    public function getGuestEmailTemplate($store = null)
    {
        return $this->getConfig('prgdpr/email/guest_email_template', $store);
    }

    /**
     * @deprecated since 3.0.0
     * @see \Plumrocket\DataPrivacy\Helper\Config\PrivacyCenterDashboard::isAvailableToGuests()
     *
     * @param null $store
     * @return bool
     */
    public function isGuestModeEnabled($store = null): bool
    {
        return (bool) $this->getConfig('prgdpr/dashboard/guest_enable', $store);
    }

    /**
     * @return string
     * @deprecated since 3.2.0
     */
    public function getMyconsentManagePageUrl()
    {
        return $this->urlBuilder->getUrl('prgdpr/consentcheckboxes/manage');
    }
}
