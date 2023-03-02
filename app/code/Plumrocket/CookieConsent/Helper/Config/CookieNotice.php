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
class CookieNotice extends ConfigUtils
{
    public const XML_PATH_DISPLAY_STYLE = 'pr_cookie/cookie_notice/display_style';
    public const XML_PATH_HIDE_ON_URLS = 'pr_cookie/cookie_notice/hide_on_urls';
    public const XML_PATH_TITLE = 'pr_cookie/cookie_notice/title';
    public const XML_PATH_TEXT = 'pr_cookie/cookie_notice/text';

    public const XML_PATH_ACCEPT_BUTTON_GROUP = 'pr_cookie/cookie_notice/accept_button';
    public const XML_PATH_DECLINE_BUTTON_GROUP = 'pr_cookie/cookie_notice/decline_button';
    public const XML_PATH_SETTINGS_BUTTON_GROUP = 'pr_cookie/cookie_notice/settings_button';
    public const XML_PATH_TITLE_COLOR = 'pr_cookie/cookie_notice/title_color';
    public const XML_PATH_TEXT_COLOR = 'pr_cookie/cookie_notice/text_color';
    public const XML_PATH_BACKGROUND_COLOR = 'pr_cookie/cookie_notice/background_color';
    public const XML_PATH_OVERLAY_BACKGROUND_COLOR = 'pr_cookie/cookie_notice/overlay_background_color';
    public const XML_PATH_OVERLAY_BLUR = 'pr_cookie/cookie_notice/overlay_blur';

    /**
     * @since 1.1.0
     * @param null $store
     * @param null $scope
     * @return string
     */
    public function getDisplayStyle($store = null, $scope = null): string
    {
        return (string) $this->getConfig(self::XML_PATH_DISPLAY_STYLE, $store, $scope);
    }

    /**
     * @param null $store
     * @param null $scope
     * @return array
     */
    public function getUrlsToHide($store = null, $scope = null): array
    {
        $value = (string) $this->getConfig(self::XML_PATH_HIDE_ON_URLS, $store, $scope);

        return array_filter($this->splitTextareaValueByLine($value));
    }

    /**
     * @since 1.1.0
     * @param null $store
     * @param null $scope
     * @return string
     */
    public function getTitle($store = null, $scope = null): string
    {
        return (string) $this->getConfig(self::XML_PATH_TITLE, $store, $scope);
    }

    /**
     * @param null $store
     * @param null $scope
     * @return string
     */
    public function getText($store = null, $scope = null): string
    {
        return (string) $this->getConfig(self::XML_PATH_TEXT, $store, $scope);
    }

    /**
     * @since 1.1.0
     * @param null $store
     * @param null $scope
     * @return string
     */
    public function getTitleColor($store = null, $scope = null): string
    {
        return (string) $this->getConfig(self::XML_PATH_TITLE_COLOR, $store, $scope);
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
     * @since 1.1.0
     * @param null $store
     * @param null $scope
     * @return string
     */
    public function getOverlayBackgroundColor($store = null, $scope = null): string
    {
        return (string) $this->getConfig(self::XML_PATH_OVERLAY_BACKGROUND_COLOR, $store, $scope);
    }

    /**
     * @since 1.1.1
     * @param null $store
     * @param null $scope
     * @return bool
     */
    public function getIsNeedBlurOverlay($store = null, $scope = null): bool
    {
        return (bool) $this->getConfig(self::XML_PATH_OVERLAY_BLUR, $store, $scope);
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
    public function getSettingsButtonConfig($store = null, $scope = null): array
    {
        return (array) $this->getConfig(self::XML_PATH_SETTINGS_BUTTON_GROUP, $store, $scope);
    }
}
