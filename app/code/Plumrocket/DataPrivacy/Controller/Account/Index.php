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
 * Settings controller.
 *
 * @since 3.1.0
 */
class Index extends AbstractPrivacyCenter
{

    /**
     * Execute controller.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $result->getConfig()->getTitle()->set(__('Welcome to the Privacy Center'));
        if (! $this->isLoggedIn()) {
            $result->getLayout()->unsetElement('sidebar.main');
        }
        return $result;
    }
}
