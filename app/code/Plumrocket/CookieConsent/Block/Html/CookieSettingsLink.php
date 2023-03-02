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

namespace Plumrocket\CookieConsent\Block\Html;

use Magento\Framework\View\Element\Html\Link;
use Magento\Framework\View\Element\Template\Context;
use Plumrocket\CookieConsent\Api\CanManageCookieInterface;

/**
 * @since 1.2.0
 */
class CookieSettingsLink extends Link
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
     * Need to overwrite parent method to avoid build the URL
     *
     * @inheritDoc
     */
    public function getHref(): string
    {
        return $this->getPath();
    }

    /**
     * If customer cannot manage cookie we should not show link
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (! $this->canManageCookie->execute()) {
            return '';
        }

        return parent::_toHtml();
    }
}
