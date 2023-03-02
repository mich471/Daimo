<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model\Consent\Validation;

use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Message\ManagerInterface;

/**
 * @since 3.1.0
 */
class RedirectResponseStrategy extends AbstractResponseStrategy
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
        ActionFlag $actionFlag,
        RedirectInterface $redirect,
        ManagerInterface $messageManager
    ) {
        parent::__construct($actionFlag);
        $this->redirect = $redirect;
        $this->messageManager = $messageManager;
    }

    /**
     * @param ResponseInterface                $response
     * @param \Magento\Framework\Phrase|string $errorMessage
     */
    protected function modifyResponse(ResponseInterface $response, $errorMessage): void
    {
        if ($errorMessage) {
            $this->messageManager->addErrorMessage($errorMessage);
        }
        $response->setRedirect($this->redirect->getRedirectUrl());
    }
}
