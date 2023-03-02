<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\DataPrivacy\Controller\Account;

use Magento\Framework\Controller\ResultFactory;
use Plumrocket\DataPrivacy\Controller\AbstractPrivacyCenter;

/**
 * Show delete customer or guest data page.
 *
 * @since 3.1.0
 */
class Delete extends AbstractPrivacyCenter
{

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $title = $this->isLoggedIn() ? __('Delete Your Account') : __('Erase Your Data');
        $result->getConfig()->getTitle()->set($title);

        if ($block = $result->getLayout()->getBlock('prgdpr_delete')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }

        if ($blockLink = $result->getLayout()->getBlock('customer-account-navigation-prgdpr-link')) {
            $blockLink->setData('is_highlighted', true);
        }

        if (! $this->isLoggedIn()) {
            $result->getLayout()->unsetElement('sidebar.main');
        }

        return $result;
    }
}
