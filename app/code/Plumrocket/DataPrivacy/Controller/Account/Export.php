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
 * @since 3.1.0
 */
class Export extends AbstractPrivacyCenter
{

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $result->getConfig()->getTitle()->set(__('Download Your Data'));

        if (! $this->isLoggedIn()) {
            $result->getLayout()->unsetElement('sidebar.main');
        }

        if ($block = $result->getLayout()->getBlock('prgdpr_export')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }

        if ($blockLink = $result->getLayout()->getBlock('customer-account-navigation-prgdpr-link')) {
            $blockLink->setData('is_highlighted', true);
        }

        return $result;
    }
}
