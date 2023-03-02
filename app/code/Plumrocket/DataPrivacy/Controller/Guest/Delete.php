<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Controller\Guest;

use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Plumrocket\DataPrivacy\Controller\AbstractPrivacyCenter;
use Plumrocket\DataPrivacy\Helper\Config;
use Plumrocket\DataPrivacy\Model\EmailSender;
use Plumrocket\DataPrivacy\Model\Guest\Access\TokenLocator;
use Plumrocket\DataPrivacy\Model\Guest\Access\Validator;
use Plumrocket\DataPrivacy\Model\RemovalRequest\GetPending;
use Plumrocket\DataPrivacy\Model\RemovalRequestFactory;
use Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest;
use Plumrocket\Token\Api\CustomerRepositoryInterface as TokenRepositoryInterface;

/**
 * Delete guest/customer data by email action.
 *
 * @since 3.1.0
 */
class Delete extends AbstractPrivacyCenter
{
    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var EmailSender
     */
    private $emailSender;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var TokenRepositoryInterface
     */
    private $tokenRepository;

    /**
     * @var \Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest
     */
    private $removalRequestResource;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Plumrocket\DataPrivacy\Model\Account\Validator
     */
    private $removeAccountValidator;

    /**
     * @var \Plumrocket\DataPrivacy\Helper\Config
     */
    private $config;

    /**
     * @var \Plumrocket\DataPrivacy\Model\RemovalRequestFactory
     */
    private $removalRequestFactory;

    /**
     * @var \Plumrocket\DataPrivacy\Model\RemovalRequest\GetPending
     */
    private $getPendingRemovalRequests;

    /**
     * @param \Magento\Framework\App\Action\Context                      $context
     * @param \Plumrocket\DataPrivacy\Model\Guest\Access\Validator       $accessValidator
     * @param \Magento\Customer\Model\Url                                $customerUrl
     * @param \Magento\Framework\App\Http\Context                        $httpContext
     * @param \Plumrocket\DataPrivacy\Model\Guest\Access\TokenLocator    $tokenLocator
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress       $remoteAddress
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                $dateTime
     * @param \Plumrocket\DataPrivacy\Model\Account\Validator            $removeAccountValidator
     * @param \Plumrocket\DataPrivacy\Model\EmailSender                  $emailSender
     * @param \Magento\Store\Model\StoreManagerInterface                 $storeManager
     * @param \Plumrocket\Token\Api\CustomerRepositoryInterface          $tokenRepository
     * @param \Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest $removalRequestResource
     * @param \Magento\Customer\Api\CustomerRepositoryInterface          $customerRepository
     * @param \Plumrocket\DataPrivacy\Helper\Config                      $config
     * @param \Plumrocket\DataPrivacy\Model\RemovalRequestFactory        $removalRequestFactory
     * @param \Plumrocket\DataPrivacy\Model\RemovalRequest\GetPending    $getPendingRemovalRequests
     */
    public function __construct(
        Context $context,
        Validator $accessValidator,
        CustomerUrl $customerUrl,
        \Magento\Framework\App\Http\Context $httpContext,
        TokenLocator $tokenLocator,
        RemoteAddress $remoteAddress,
        DateTime $dateTime,
        \Plumrocket\DataPrivacy\Model\Account\Validator $removeAccountValidator,
        EmailSender $emailSender,
        StoreManagerInterface $storeManager,
        TokenRepositoryInterface $tokenRepository,
        RemovalRequest $removalRequestResource,
        CustomerRepositoryInterface $customerRepository,
        Config $config,
        RemovalRequestFactory $removalRequestFactory,
        GetPending $getPendingRemovalRequests
    ) {
        parent::__construct($context, $accessValidator, $customerUrl, $httpContext, $tokenLocator);

        $this->remoteAddress = $remoteAddress;
        $this->dateTime = $dateTime;
        $this->emailSender = $emailSender;
        $this->storeManager = $storeManager;
        $this->tokenRepository = $tokenRepository;
        $this->removalRequestResource = $removalRequestResource;
        $this->customerRepository = $customerRepository;
        $this->removeAccountValidator = $removeAccountValidator;
        $this->config = $config;
        $this->removalRequestFactory = $removalRequestFactory;
        $this->getPendingRemovalRequests = $getPendingRemovalRequests;
    }

    /**
     * Create removal request for guest or customer.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $jsonResult */
        $jsonResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $token = $this->getRequest()->getParam('token');

        try {
            $guestEmail = $this->tokenRepository->get($token)->getEmail();
        } catch (NoSuchEntityException $e) {
            $this->messageManager
                ->addErrorMessage(__('Something went wrong, please try again later!'));
            return $this->resultRedirectFactory->create()->setPath('pr_data_privacy/account/delete');
        }

        if (! $guestEmail) {
            $this->messageManager->addErrorMessage(__('Email cannot be empty.'));
            return $jsonResult->setHttpResponseCode(400);
        }

        try {
            $existingCustomer = $this->customerRepository->get(
                $guestEmail,
                $this->storeManager->getStore()->getWebsiteId()
            );
        } catch (NoSuchEntityException $e) {
            $existingCustomer = false;
        } catch (LocalizedException $e) {
            $this->messageManager
                ->addErrorMessage(__('Something went wrong, please try again later!'));
            return $this->resultRedirectFactory->create()->setPath('pr_data_privacy/account/delete');
        }

        if ($existingCustomer) {
            if ($this->getPendingRemovalRequests->getForCustomer((int) $existingCustomer->getId())) {
                $this->messageManager->addErrorMessage(__('Your account already scheduled for data deletion.'));
                return $jsonResult->setHttpResponseCode(400);
            }

            if ($this->removeAccountValidator->hasCustomerOpenedOrders((int)$existingCustomer->getId())) {
                $this->messageManager->addErrorMessage(
                    __("This account cannot be deleted because some orders are still pending. "
                        . "Please complete or cancel all orders before deleting your account.")
                );

                return $jsonResult->setHttpResponseCode(400);
            }

            try {
                /** @var \Plumrocket\DataPrivacy\Model\RemovalRequest $removalRequest */
                $removalRequest = $this->removalRequestFactory
                    ->create()
                    ->setData(
                        [
                            'created_at'     => date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp()),
                            'scheduled_at'   => date(
                                'Y-m-d H:i:s',
                                $this->dateTime->gmtTimestamp() + $this->config->getDeletionTime()
                            ),
                            'customer_id'    => $existingCustomer->getId(),
                            'customer_email' => $existingCustomer->getEmail(),
                            'customer_ip'    => $this->remoteAddress->getRemoteAddress(),
                            'website_id'     => $this->storeManager->getStore()->getWebsiteId(),
                        ]
                    );

                $this->removalRequestResource->save($removalRequest);
                $this->emailSender->sendRemovalRequestNotification($existingCustomer);
                return $jsonResult->setData(['errors' => false]);
            } catch (LocalizedException | Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong, please try again later!'));
                return $jsonResult->setHttpResponseCode(500);
            }
        }

        if ($this->getPendingRemovalRequests->getForGuest($guestEmail)) {
            $this->messageManager->addErrorMessage(__('Your account already scheduled for data deletion.'));
            return $jsonResult->setHttpResponseCode(400);
        }

        if ($this->removeAccountValidator->hasGuestOpenedOrders($guestEmail)) {
            $this->messageManager->addErrorMessage(
                __('This account cannot be deleted because some orders are still pending. '
                    . 'Please complete or cancel all orders before deleting your account.')
            );
            return $jsonResult->setData(['ERROR!'])->setHttpResponseCode(400);
        }

        try {
            /** @var \Plumrocket\DataPrivacy\Model\RemovalRequest $removalRequest */
            $removalRequest = $this->removalRequestFactory
                ->create()
                ->setData(
                    [
                        'created_at'     => date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp()),
                        'scheduled_at'   => date(
                            'Y-m-d H:i:s',
                            $this->dateTime->gmtTimestamp() + $this->config->getDeletionTime()
                        ),
                        'customer_id'    => 0,
                        'customer_email' => $guestEmail,
                        'customer_ip'    => $this->remoteAddress->getRemoteAddress(),
                        'website_id'     => $this->storeManager->getStore()->getWebsiteId(),
                    ]
                );

            $this->removalRequestResource->save($removalRequest);
            $this->emailSender->sendGuestRemovalRequestNotification($guestEmail);
            $response = ['errors' => false];
        } catch (LocalizedException | Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong, please try again later!'));
            return $jsonResult->setHttpResponseCode(500);
        }

        return $jsonResult->setData($response);
    }
}
