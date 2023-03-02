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

use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Plumrocket\Base\Model\Utils\IsMatchUrl;
use Plumrocket\CookieConsent\Api\CanManageCookieInterface;
use Plumrocket\CookieConsent\Helper\Config\CookieNotice as CookieNoticeConfig;

/**
 * @since 1.0.0
 */
class CookieNotice extends Template
{
    const COOKIE_NAME_STATUS = 'pr-cookie-notice-status';

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Plumrocket\CookieConsent\Api\CanManageCookieInterface
     */
    private $canManageCookie;

    /**
     * @var \Plumrocket\Base\Model\Utils\IsMatchUrl
     */
    private $isMatchUrl;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    private $filterProvider;

    /**
     * @var \Plumrocket\CookieConsent\Helper\Config\CookieNotice
     */
    private $cookieNoticeConfig;

    /**
     * @param \Magento\Framework\View\Element\Template\Context       $context
     * @param \Magento\Framework\Serialize\SerializerInterface       $serializer
     * @param \Plumrocket\CookieConsent\Api\CanManageCookieInterface $canManageCookie
     * @param \Plumrocket\Base\Model\Utils\IsMatchUrl                $isMatchUrl
     * @param \Magento\Cms\Model\Template\FilterProvider             $filterProvider
     * @param \Plumrocket\CookieConsent\Helper\Config\CookieNotice   $cookieNoticeConfig
     * @param array                                                  $data
     */
    public function __construct(
        Context $context,
        SerializerInterface $serializer,
        CanManageCookieInterface $canManageCookie,
        IsMatchUrl $isMatchUrl,
        FilterProvider $filterProvider,
        CookieNoticeConfig $cookieNoticeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->serializer = $serializer;
        $this->canManageCookie = $canManageCookie;
        $this->isMatchUrl = $isMatchUrl;
        $this->filterProvider = $filterProvider;
        $this->cookieNoticeConfig = $cookieNoticeConfig;
    }

    /**
     * @return array
     */
    public function getJsComponentConfig(): array
    {
        return [
            'displayStyle' => $this->cookieNoticeConfig->getDisplayStyle(),
            'acceptButtonConfig' => $this->prepareButtonConfig($this->cookieNoticeConfig->getAcceptButtonConfig()),
            'declineButtonConfig' => $this->prepareButtonConfig($this->cookieNoticeConfig->getDeclineButtonConfig()),
            'settingsButtonConfig' => $this->prepareButtonConfig($this->cookieNoticeConfig->getSettingsButtonConfig()),
            'statusCookieName' => self::COOKIE_NAME_STATUS,
            'noticeTitle' => $this->cookieNoticeConfig->getTitle(),
            'noticeTextHtml' => $this->filterProvider->getBlockFilter()->filter(
                $this->cookieNoticeConfig->getText()
            ),
            'design' => [
                'titleColor' => $this->cookieNoticeConfig->getTitleColor(),
                'textColor' => $this->cookieNoticeConfig->getTextColor(),
                'backgroundColor' => $this->cookieNoticeConfig->getBackgroundColor(),
                'overlayBackgroundColor' => $this->cookieNoticeConfig->getOverlayBackgroundColor(),
                'overlayBlur' => $this->cookieNoticeConfig->getIsNeedBlurOverlay(),
            ],
        ];
    }

    /**
     * @return bool
     */
    public function canManageCookie(): bool
    {
        return $this->canManageCookie->execute();
    }

    /**
     * @return bool
     */
    public function isAllowedPage(): bool
    {
        return ! $this->isMatchUrl->executeList(
            $this->_urlBuilder->getCurrentUrl(),
            $this->cookieNoticeConfig->getUrlsToHide()
        );
    }

    /**
     * @return false|string
     */
    public function getJsLayout()
    {
        if (isset($this->jsLayout['components']['pr-cookie-notice'])) {
            $this->jsLayout['components']['pr-cookie-notice'] = array_merge_recursive(
                $this->jsLayout['components']['pr-cookie-notice'],
                $this->getJsComponentConfig()
            );
        } else {
            $this->jsLayout['components']['pr-cookie-notice'] = $this->getJsComponentConfig();
        }

        return $this->jsLayout ? $this->serializer->serialize($this->jsLayout) : '';
    }

    /**
     * @param array $config
     * @return array
     */
    private function prepareButtonConfig(array $config): array
    {
        if (isset($config['enabled'])) {
            $config['enabled'] = (bool) $config['enabled'];
        }

        return $config;
    }
}
