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

namespace Plumrocket\CookieConsent\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Plumrocket\CookieConsent\Helper\Config;

/**
 * We create own block for cookie notice therefore need to remove old
 *
 * @since 1.0.0
 */
class RemoveMagentoNotice implements ObserverInterface
{
    /**
     * @var \Plumrocket\CookieConsent\Helper\Config
     */
    private $config;

    /**
     * @param \Plumrocket\CookieConsent\Helper\Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (! $this->config->isModuleEnabled()) {
            return;
        }

        /**
         * @var \Magento\Framework\View\LayoutInterface $layout
         */
        $layout = $observer->getEvent()->getLayout();

        $layout->getUpdate()->addUpdate('<referenceBlock name="cookie_notices" remove="true"/>');
    }
}
