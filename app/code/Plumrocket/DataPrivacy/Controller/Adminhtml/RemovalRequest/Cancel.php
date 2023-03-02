<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Controller\Adminhtml\RemovalRequest;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Plumrocket\DataPrivacy\Model\OptionSource\RemovalStatus;
use Plumrocket\DataPrivacyApi\Api\RemovalRequestRepositoryInterface;

/**
 * @since 3.2.0
 */
class Cancel extends Action
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Plumrocket_GDPR::removalrequests_cancel';

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var Session
     */
    private $authSession;

    /**
     * @var \Plumrocket\DataPrivacyApi\Api\RemovalRequestRepositoryInterface
     */
    private $removalRequestRepository;

    /**
     * @param \Magento\Backend\App\Action\Context                              $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                      $dateTime
     * @param \Magento\Backend\Model\Auth\Session                              $authSession
     * @param \Plumrocket\DataPrivacyApi\Api\RemovalRequestRepositoryInterface $removalRequestRepository
     */
    public function __construct(
        Context $context,
        DateTime $dateTime,
        Session $authSession,
        RemovalRequestRepositoryInterface $removalRequestRepository
    ) {
        parent::__construct($context);
        $this->dateTime = $dateTime;
        $this->authSession = $authSession;
        $this->removalRequestRepository = $removalRequestRepository;
    }

    /**
     * Cancel action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $requestId = (int) $this->getRequest()->getParam('request_id');
        $responseType = (string) $this->getRequest()->getParam('responseType');

        try {
            $removalRequest = $this->removalRequestRepository->getById($requestId);
            if (RemovalStatus::PENDING === $removalRequest->getStatus()) {
                $removalRequest->addData([
                    'cancelled_at' => date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp()),
                    'cancelled_by' => $this->authSession->getUser()->getUserId(),
                    'scheduled_at' => null,
                ]);
                $removalRequest->setStatus(RemovalStatus::CANCELLED);
                $this->removalRequestRepository->save($removalRequest);
            }

            $error = false;
            $message = __('Account removal request has been canceled.');
        } catch (Exception $e) {
            $error = true;
            $message = $e->getMessage();
        }

        if ('json' === $responseType) {
            return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData(
                ['error' => $error, 'messages' => $message]
            );
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($error) {
            $this->messageManager->addErrorMessage($message);
        } else {
            $this->messageManager->addSuccessMessage($message);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
