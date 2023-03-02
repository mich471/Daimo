<?php
/**
 * @package     Plumrocket_magento_2_3_6__1
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Controller\Consentpopups;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;

/**
 * @since 3.1.0
 */
class Remindlater extends Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $session;

    /**
     * @param \Magento\Framework\App\Action\Context            $context
     * @param \Magento\Customer\Model\Session                  $session
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        Session $session,
        ResultJsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->session = $session;
    }

    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        $this->session->setData('prgdpr_remindlater_notifys', true);

        $response = ['error' => false];

        return $resultJson->setData($response);
    }
}
