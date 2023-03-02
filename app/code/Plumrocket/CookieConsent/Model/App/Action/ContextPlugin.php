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

namespace Plumrocket\CookieConsent\Model\App\Action;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Module\Manager;
use Magento\PageCache\Model\Config;
use Plumrocket\CookieConsent\Api\CanManageCookieInterface;

/**
 * Plugin before \Magento\Framework\App\Action\AbstractAction::dispatch.
 *
 * Create separate cache for visitors we can manage cookie
 */
class ContextPlugin
{
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\PageCache\Model\Config
     */
    protected $cacheConfig;

    /**
     * @var \Plumrocket\CookieConsent\Api\CanManageCookieInterface
     */
    private $canManageCookie;

    /**
     * ContextPlugin constructor.
     *
     * @param \Magento\Framework\App\Http\Context                    $httpContext
     * @param \Magento\Framework\Module\Manager                      $moduleManager
     * @param \Magento\PageCache\Model\Config                        $cacheConfig
     * @param \Plumrocket\CookieConsent\Api\CanManageCookieInterface $canManageCookie
     */
    public function __construct(
        Context $httpContext,
        Manager $moduleManager,
        Config $cacheConfig,
        CanManageCookieInterface $canManageCookie
    ) {
        $this->httpContext = $httpContext;
        $this->moduleManager = $moduleManager;
        $this->cacheConfig = $cacheConfig;
        $this->canManageCookie = $canManageCookie;
    }

    /**
     * Before dispatch.
     *
     * @param \Magento\Framework\App\ActionInterface $subject
     * @param \Magento\Framework\App\RequestInterface $request
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeDispatch(ActionInterface $subject, RequestInterface $request)
    {
        if (! $this->moduleManager->isEnabled('Magento_PageCache') ||
            ! $this->cacheConfig->isEnabled()
        ) {
            return;
        }

        $this->httpContext->setValue(
            'pr_allow_manage_cookie',
            (int) $this->canManageCookie->execute(),
            0
        );
    }
}
