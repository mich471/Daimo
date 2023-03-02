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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Model\Consent\Validation;

use Magento\Framework\App\ResponseInterface;

class RedirectResponseStrategy extends \Plumrocket\GDPR\Model\Consent\Validation\AbstractResponseStrategy
{
    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    private $redirect;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * DefaultResponseStrategy constructor.
     *
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Framework\App\ActionFlag                 $actionFlag
     * @param \Magento\Framework\Message\ManagerInterface       $messageManager
     */
    public function __construct(
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        parent::__construct($actionFlag);
        $this->redirect = $redirect;
        $this->messageManager = $messageManager;
    }

    /**
     * @param ResponseInterface                $response
     * @param \Magento\Framework\Phrase|string $errorMessage
     */
    protected function modifyResponse(ResponseInterface $response, $errorMessage) //@codingStandardsIgnoreLine
    {
        if ($errorMessage) {
            $this->messageManager->addErrorMessage($errorMessage);
        }

        $response->setRedirect($this->redirect->getRedirectUrl());
    }
}
