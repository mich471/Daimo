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
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;
use Plumrocket\DataPrivacy\Model\Account\Validator;
use Plumrocket\DataPrivacy\Model\EmailSender;
use Plumrocket\DataPrivacy\Model\RemovalRequest\CreateByAdmin;
use Plumrocket\DataPrivacy\Model\RemovalRequest\GetPending;

/**
 * @since 3.2.0
 */
class Create extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Plumrocket_GDPR::removalrequests';

    /**
     * @var \Plumrocket\DataPrivacy\Model\EmailSender
     */
    private $emailSender;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Plumrocket\DataPrivacy\Model\Account\Validator
     */
    private $removeAccountValidator;

    /**
     * @var \Plumrocket\DataPrivacy\Model\RemovalRequest\CreateByAdmin
     */
    private $createByAdmin;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $authSession;

    /**
     * @var \Plumrocket\DataPrivacy\Model\RemovalRequest\GetPending
     */
    private $getPendingRemovalRequests;

    /**
     * @param \Magento\Backend\App\Action\Context                        $context
     * @param \Plumrocket\DataPrivacy\Model\EmailSender                  $emailSender
     * @param \Plumrocket\DataPrivacy\Model\Account\Validator            $removeAccountValidator
     * @param \Magento\Customer\Api\CustomerRepositoryInterface          $customerRepository
     * @param \Plumrocket\DataPrivacy\Model\RemovalRequest\CreateByAdmin $createByAdmin
     * @param \Magento\Backend\Model\Auth\Session                        $authSession
     * @param \Plumrocket\DataPrivacy\Model\RemovalRequest\GetPending    $getPendingRemovalRequests
     */
    public function __construct(
        Context $context,
        EmailSender $emailSender,
        Validator $removeAccountValidator,
        CustomerRepositoryInterface $customerRepository,
        CreateByAdmin $createByAdmin,
        Session $authSession,
        GetPending $getPendingRemovalRequests
    ) {
        parent::__construct($context);
        $this->emailSender = $emailSender;
        $this->customerRepository = $customerRepository;
        $this->removeAccountValidator = $removeAccountValidator;
        $this->createByAdmin = $createByAdmin;
        $this->authSession = $authSession;
        $this->getPendingRemovalRequests  = $getPendingRemovalRequests;
    }

    /**
     * Create removal request.
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $customerId = (int) $this->_request->getParam('customer_id');
        $adminComment = (string) $this->_request->getParam('comment');
        /** @var \Magento\Framework\Controller\Result\Json $jsonResult */
        $jsonResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        try {
            $currentCustomer = $this->customerRepository->getById($customerId);
            if ($this->removeAccountValidator->hasCustomerOpenedOrders($customerId)) {
                $message = __("This account cannot be deleted because some orders are still pending. "
                              . "Please complete or cancel all orders before deleting your account.");
                return $jsonResult->setData(['error' => true, 'messages' => (string) $message]);
            }

            if ($this->getPendingRemovalRequests->getForCustomer($customerId)) {
                return $jsonResult->setData(
                    [
                        'error' => true,
                        'messages' => (string) __('This customer already scheduled for data deletion.')
                    ]
                );
            }

            $removalRequest = $this->createByAdmin->execute($customerId, $this->authSession->getUser(), $adminComment);
            $this->emailSender->sendAdminRemovalRequestNotification($currentCustomer, $removalRequest);
            return $jsonResult->setData(['error' => false, 'messages' => __('Removal request was created.')]);
        } catch (Exception $e) {
            return $jsonResult->setData(
                [
                    'error' => true,
                    'messages' => (string) __('Something went wrong, please try again later!')
                ]
            );
        }
    }
}
