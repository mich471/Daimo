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
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Controller\Adminhtml\Consent;

abstract class Checkbox extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Plumrocket_GDPR::consent_checkbox';

    /**
     * Init Consent Location
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initConsentLocation($resultPage) //@codingStandardsIgnoreLine
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
                   ->addBreadcrumb(__('GDPR'), __('GDPR'))
                   ->addBreadcrumb(__('Checkbox'), __('Checkbox'));

        return $resultPage;
    }
}
