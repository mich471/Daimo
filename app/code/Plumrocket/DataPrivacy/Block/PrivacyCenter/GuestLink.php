<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Block\PrivacyCenter;

use Magento\Framework\View\Element\Html\Link\Current;

/**
 * @since 3.2.0
 */
class GuestLink extends Current
{

    /**
     * Remove guest Data Privacy link if access is disabled in config.
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (! $this->_scopeConfig->isSetFlag('prgdpr/dashboard/guest_enable')
            || ! $this->_scopeConfig->isSetFlag('prgdpr/general/enabled')
        ) {
            return '';
        }

        return parent::_toHtml();
    }
}
