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

namespace Plumrocket\CookieConsent\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;

/**
 * @since 1.0.0
 */
abstract class AbstractItem extends Action
{
    const ADMIN_RESOURCE = 'Plumrocket_CookieConsent::cookie_categories';

    /**
     * Init Consent Location
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initBreadcrumbAndMenu($resultPage): Page
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
                   ->addBreadcrumb(__('Cookie Consent'), __('Cookie Consent'))
                   ->addBreadcrumb(__('Cookie'), __('Cookie'));

        return $resultPage;
    }
}
