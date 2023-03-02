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

namespace Plumrocket\CookieConsent\Helper\Config;

use Plumrocket\Base\Helper\ConfigUtils;

/**
 * @since 1.0.0
 */
class SettingsBar extends ConfigUtils
{
    public const XML_PATH_SHOW_COOKIE_DETAILS = 'pr_cookie/settings_bar/show_details';
    public const XML_PATH_OVERVIEW_TITLE = 'pr_cookie/settings_bar/overview_title';
    public const XML_PATH_OVERVIEW_TEXT = 'pr_cookie/settings_bar/overview_text';
    public const XML_PATH_TEXT_COLOR = 'pr_cookie/settings_bar/text_color';
    public const XML_PATH_BACKGROUND_COLOR = 'pr_cookie/settings_bar/background_color';

    public const XML_PATH_ACCEPT_BUTTON_GROUP = 'pr_cookie/settings_bar/accept_button';
    public const XML_PATH_DECLINE_BUTTON_GROUP = 'pr_cookie/settings_bar/decline_button';
    public const XML_PATH_CONFIRM_BUTTON_GROUP = 'pr_cookie/settings_bar/confirm_button';

    /**
     * @param null $store
     * @param null $scope
     * @return bool
     */
    public function canShowCookieDetails($store = null, $scope = null): bool
    {
        return (bool) $this->getConfig(self::XML_PATH_SHOW_COOKIE_DETAILS, $store, $scope);
    }

    /**
     * @param null $store
     * @param null $scope
     * @return string
     */
    public function getOverviewTitle($store = null, $scope = null): string
    {
        return (string) $this->getConfig(self::XML_PATH_OVERVIEW_TITLE, $store, $scope);
    }

    /**
     * @param null $store
     * @param null $scope
     * @return string
     */
    public function getOverviewText($store = null, $scope = null): string
    {
        return (string) $this->getConfig(self::XML_PATH_OVERVIEW_TEXT, $store, $scope);
    }

    /**
     * @param null $store
     * @param null $scope
     * @return string
     */
    public function getTextColor($store = null, $scope = null): string
    {
        return (string) $this->getConfig(self::XML_PATH_TEXT_COLOR, $store, $scope);
    }

    /**
     * @param null $store
     * @param null $scope
     * @return string
     */
    public function getBackgroundColor($store = null, $scope = null): string
    {
        return (string) $this->getConfig(self::XML_PATH_BACKGROUND_COLOR, $store, $scope);
    }

    /**
     * @param null $store
     * @param null $scope
     * @return array
     */
    public function getAcceptButtonConfig($store = null, $scope = null): array
    {
        return (array) $this->getConfig(self::XML_PATH_ACCEPT_BUTTON_GROUP, $store, $scope);
    }

    /**
     * @param null $store
     * @param null $scope
     * @return array
     */
    public function getDeclineButtonConfig($store = null, $scope = null): array
    {
        return (array) $this->getConfig(self::XML_PATH_DECLINE_BUTTON_GROUP, $store, $scope);
    }

    /**
     * @param null $store
     * @param null $scope
     * @return array
     */
    public function getConfirmButtonConfig($store = null, $scope = null): array
    {
        return (array) $this->getConfig(self::XML_PATH_CONFIRM_BUTTON_GROUP, $store, $scope);
    }
}
