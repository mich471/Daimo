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

namespace Plumrocket\CookieConsent\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Plumrocket\CookieConsent\Helper\Config;

/**
 * Google Tag Manager Page Block
 */
class Gtm extends Template
{
    /**
     * @var \Plumrocket\CookieConsent\Helper\Config
     */
    private $config;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Plumrocket\CookieConsent\Helper\Config          $config
     * @param array                                            $data
     */
    public function __construct(
        Context $context,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
    }

    /**
     * Render GTM tracking scripts
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (! $this->config->isGtmEnabled()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * Return cookie restriction mode value.
     *
     * @return string
     */
    public function getGtmContainerId(): string
    {
        return $this->config->getGtmContainerId();
    }

    /**
     * Return cookie restriction mode value.
     *
     * @return string
     */
    public function getGmtAssociatedCategoryKey(): string
    {
        return $this->config->getGmtAssociatedCategoryKey();
    }
}
