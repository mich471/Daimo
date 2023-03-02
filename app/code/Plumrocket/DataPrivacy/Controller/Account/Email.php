<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Plumrocket\DataPrivacy\Helper\Config;
use Plumrocket\GDPR\Helper\Guest\Data as GuestDataHelper;
use Plumrocket\GDPR\Model\EmailSender;
use Zend_Validate;

/**
 * @since 3.1.0
 */
class Email extends Action
{
    /**
     * @var \Plumrocket\DataPrivacy\Helper\Config
     */
    private $config;

    /**
     * @var \Plumrocket\GDPR\Helper\Guest\Data
     */
    private $guestHelper;

    /**
     * @var \Plumrocket\GDPR\Model\EmailSender
     */
    private $emailSender;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Plumrocket\GDPR\Helper\Guest\Data    $guestHelper
     * @param \Plumrocket\DataPrivacy\Helper\Config $config
     * @param \Plumrocket\GDPR\Model\EmailSender    $emailSender
     */
    public function __construct(
        Context $context,
        GuestDataHelper $guestHelper,
        Config $config,
        EmailSender $emailSender
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->guestHelper = $guestHelper;
        $this->emailSender = $emailSender;
    }

    public function execute()
    {
        if (! $this->config->isModuleEnabled()) {
            return;
        }

        $data = ['success' => false, 'email' => '', 'message' => ''];
        $email = $this->getRequest()->getParam('email');
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        if (! empty($email)) {
            $email = trim($email);
            $data['email'] = $email;

            if (Zend_Validate::is($email, 'EmailAddress')) {
                try {
                    if ($this->guestHelper->emailExistInDb($email)) {
                        $this->emailSender->sendGuestRequestEmail($email);
                        $data['success'] = true;
                    }
                } catch (\Exception $e) {
                    $data['message'] = $e->getMessage();
                }
            }
        }

        return $result->setData($data);
    }
}
