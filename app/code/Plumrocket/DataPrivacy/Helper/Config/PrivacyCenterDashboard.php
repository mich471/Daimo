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
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Helper\Config;

use Magento\Cms\Helper\Page;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\Session;
use Plumrocket\Base\Helper\ConfigUtils;
use Plumrocket\DataPrivacyApi\Api\CheckboxProviderInterface;
use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;

/**
 * @since 1.0.0
 */
class PrivacyCenterDashboard extends ConfigUtils
{
    const XML_PATH_PRIVACY_POLICY_PAGE_ID = 'prgdpr/dashboard/privacy_policy_page';
    const XML_PATH_COOKIE_POLICY_PAGE_ID = 'prgdpr/dashboard/cookie_policy_page';
    const XML_PATH_AVAILABLE_TO_GUESTS = 'prgdpr/dashboard/guest_enable';
    const XML_PATH_GUEST_LINK_EXPIRATION = 'prgdpr/dashboard/guest_expiration_link';

    /**
     * @var \Magento\Cms\Helper\Page
     */
    private $cmsPageHelper;

    /**
     * @var \Plumrocket\GDPR\Api\CheckboxProviderInterface
     */
    private $checkboxProvider;

    /**
     * @var SessionManagerInterface
     */
    private $customerSession;

    /**
     * @param \Magento\Framework\App\Helper\Context          $context
     * @param \Magento\Cms\Helper\Page                       $cmsPageHelper
     * @param \Plumrocket\GDPR\Api\CheckboxProviderInterface $checkboxProvider
     * @param \Magento\Customer\Model\Session                $customerSession
     */
    public function __construct(
        Context $context,
        Page $cmsPageHelper,
        CheckboxProviderInterface $checkboxProvider,
        Session $customerSession
    ) {
        parent::__construct($context);
        $this->cmsPageHelper = $cmsPageHelper;
        $this->checkboxProvider = $checkboxProvider;
        $this->customerSession = $customerSession;
    }

    /**
     * @param null $store
     * @param null $scope
     * @return int
     */
    public function getPrivacyPolicyPageId($store = null, $scope = null): int
    {
        return (int) $this->getConfig(self::XML_PATH_PRIVACY_POLICY_PAGE_ID, $store, $scope);
    }

    /**
     * @param null $store
     * @param null $scope
     * @return string
     */
    public function getPrivacyPolicyPageUrl($store = null, $scope = null): string
    {
        return (string) $this->cmsPageHelper->getPageUrl($this->getPrivacyPolicyPageId($store, $scope));
    }

    /**
     * @param null $store
     * @param null $scope
     * @return int
     */
    public function getCookiePolicyPageId($store = null, $scope = null): int
    {
        return (int) $this->getConfig(self::XML_PATH_COOKIE_POLICY_PAGE_ID, $store, $scope);
    }

    /**
     * @param null $store
     * @param null $scope
     * @return string
     */
    public function getCookiePolicyPageUrl($store = null, $scope = null): string
    {
        return (string) $this->cmsPageHelper->getPageUrl($this->getCookiePolicyPageId($store, $scope));
    }

    /**
     * @param null $store
     * @param null $scope
     * @return bool
     */
    public function isAvailableToGuests($store = null, $scope = null): bool
    {
        return (bool) $this->getConfig(self::XML_PATH_AVAILABLE_TO_GUESTS, $store, $scope);
    }

    /**
     * @param null $store
     * @param null $scope
     * @return int
     */
    public function getGuestLinkExpiration($store = null, $scope = null): int
    {
        return (int) $this->getConfig(self::XML_PATH_GUEST_LINK_EXPIRATION, $store, $scope);
    }

    /**
     * @return bool
     */
    public function showMyConsentsPage(): bool
    {
        return $this->customerSession->isLoggedIn()
            && ! empty($this->checkboxProvider->getEnabled(ConsentLocations::MY_ACCOUNT));
    }
}
