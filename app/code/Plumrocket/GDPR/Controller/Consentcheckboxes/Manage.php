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

declare(strict_types=1);

namespace Plumrocket\GDPR\Controller\Consentcheckboxes;

use Magento\Framework\App\RequestInterface;

class Manage extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * Manage constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Session\SessionManagerInterface $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Session\SessionManagerInterface $customerSession
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
    }

    /**
     * @inheritDoc
     */
    public function dispatch(RequestInterface $request)
    {
        if (! $this->customerSession->isLoggedIn()) {
            $this->getActionFlag()->set('', self::FLAG_NO_DISPATCH, true);
            $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_FORWARD);
            return $result->forward('noroute');
        }

        return parent::dispatch($request);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $result->getConfig()->getTitle()->set(__('My Consents'));
        return $result;
    }
}
