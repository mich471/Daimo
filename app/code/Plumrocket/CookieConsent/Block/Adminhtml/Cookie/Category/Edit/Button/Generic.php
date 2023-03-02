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

namespace Plumrocket\CookieConsent\Block\Adminhtml\Cookie\Category\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Plumrocket\CookieConsent\Api\Data\CategoryInterface;
use Plumrocket\CookieConsent\Ui\Locator\CategoryLocatorInterface;

/**
 * @since 1.0.0
 */
class Generic implements ButtonProviderInterface
{
    /**
     * Url Builder
     *
     * @var Context
     */
    protected $context;

    /**
     * @var \Plumrocket\CookieConsent\Ui\Locator\CategoryLocatorInterface
     */
    private $categoryLocator;

    /**
     * Generic constructor
     *
     * @param Context                                                       $context
     * @param \Plumrocket\CookieConsent\Ui\Locator\CategoryLocatorInterface $categoryLocator
     */
    public function __construct(
        Context $context,
        CategoryLocatorInterface $categoryLocator
    ) {
        $this->context = $context;
        $this->categoryLocator = $categoryLocator;
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
     * @return \Plumrocket\CookieConsent\Api\Data\CategoryInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getCategory(): CategoryInterface
    {
        return $this->categoryLocator->getCategory();
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [];
    }
}
