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

namespace Plumrocket\CookieConsent\Block\Adminhtml\Cookie\Item\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Plumrocket\CookieConsent\Api\Data\CookieInterface;
use Plumrocket\CookieConsent\Ui\Locator\CookieLocatorInterface;

/**
 * @since 1.0.0
 */
class Generic implements ButtonProviderInterface
{
    /**W
     * Url Builder
     *
     * @var Context
     */
    protected $context;

    /**
     * @var \Plumrocket\CookieConsent\Ui\Locator\CookieLocatorInterface
     */
    private $cookieLocator;

    /**
     * Generic constructor
     *
     * @param Context                                                     $context
     * @param \Plumrocket\CookieConsent\Ui\Locator\CookieLocatorInterface $cookieLocator
     */
    public function __construct(
        Context $context,
        CookieLocatorInterface $cookieLocator
    ) {
        $this->context = $context;
        $this->cookieLocator = $cookieLocator;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrl($route, $params);
    }

    /**
     * @return \Plumrocket\CookieConsent\Api\Data\CookieInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getCookie(): CookieInterface
    {
        return $this->cookieLocator->getCookie();
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [];
    }
}
