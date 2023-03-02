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
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;
use Plumrocket\CookieConsent\Api\CanManageCookieInterface;

/**
 * @since 1.2.0
 */
class CookieSettingsButton extends Template implements BlockInterface
{
    /**
     * @var \Plumrocket\CookieConsent\Api\CanManageCookieInterface
     */
    private $canManageCookie;

    /**
     * @param \Magento\Framework\View\Element\Template\Context       $context
     * @param \Plumrocket\CookieConsent\Api\CanManageCookieInterface $canManageCookie
     * @param array                                                  $data
     */
    public function __construct(
        Context $context,
        CanManageCookieInterface $canManageCookie,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->canManageCookie = $canManageCookie;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->_getData('title') ?: 'Cookie Settings';
    }

    /**
     * @return string
     */
    protected function _toHtml(): string
    {
        if (! $this->canManageCookie->execute()) {
            return '';
        }

        return (string) parent::_toHtml();
    }
}
