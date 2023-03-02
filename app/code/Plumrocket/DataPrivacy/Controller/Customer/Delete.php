<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Controller\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\AuthenticationInterface;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Serialize\SerializerInterface;
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

/**
 * Delete customer data action.
 *
 * @since 3.1.0
 */
class Delete extends AbstractPrivacyCenter
{

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var \Plumrocket\DataPrivacy\Model\EmailSender
     */
    private $emailSender;

    /**
     * @var \Plumrocket\DataPrivacy\Model\RemovalRequestFactory
     */
    private $removalRequestFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\RemovalRequests
     */
    private $removalRequestResource;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Magento\Customer\Model\AuthenticationInterface
     */
    private $authentication;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Plumrocket\DataPrivacy\Helper\Config
     */
    private $config;

    /**
     * @var \Plumrocket\DataPrivacy\Model\Account\Validator
     */
    private $removeAccountValidator;

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
     * @param \Magento\Customer\Model\Session                            $customerSession
     * @param \Plumrocket\DataPrivacy\Model\EmailSender                  $emailSender
     * @param \Plumrocket\DataPrivacy\Model\Account\Validator            $removeAccountValidator
     * @param \Plumrocket\DataPrivacy\Model\RemovalRequestFactory        $removalRequestFactory
     * @param \Magento\Store\Model\StoreManagerInterface                 $storeManager
     * @param \Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest $removalRequestResource
     * @param \Magento\Customer\Api\CustomerRepositoryInterface          $customerRepository
     * @param \Magento\Framework\Serialize\SerializerInterface           $serializer
     * @param \Magento\Customer\Model\AuthenticationInterface            $authentication
     * @param \Plumrocket\DataPrivacy\Helper\Config                      $config
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
        Session $customerSession,
        EmailSender $emailSender,
        \Plumrocket\DataPrivacy\Model\Account\Validator $removeAccountValidator,
        RemovalRequestFactory $removalRequestFactory,
        StoreManagerInterface $storeManager,
        RemovalRequest $removalRequestResource,
        CustomerRepositoryInterface $customerRepository,
        SerializerInterface $serializer,
        AuthenticationInterface $authentication,
        Config $config,
        GetPending $getPendingRemovalRequests
    ) {
        parent::__construct($context, $accessValidator, $customerUrl, $httpContext, $tokenLocator);

        $this->remoteAddress = $remoteAddress;
        $this->dateTime = $dateTime;
        $this->emailSender = $emailSender;
        $this->removalRequestFactory = $removalRequestFactory;
        $this->storeManager = $storeManager;
        $this->removalRequestResource = $removalRequestResource;
        $this->customerRepository = $customerRepository;
        $this->serializer = $serializer;
        $this->authentication = $authentication;
        $this->customerSession = $customerSession;
        $this->config = $config;
        $this->removeAccountValidator = $removeAccountValidator;
        $this->getPendingRemovalRequests = $getPendingRemovalRequests;
    }

    /**
     * Create removal request for customer.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $jsonResult */
        $jsonResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        try {
            $customerId = (int) $this->customerSession->getCustomerId();
            $currentCustomer = $this->customerRepository->getById($customerId);
            $formContent = $this->serializer->unserialize($this->getRequest()->getContent());
            $this->authentication->authenticate($currentCustomer->getId(), $formContent['password']);
            if ($this->removeAccountValidator->hasCustomerOpenedOrders((int) $currentCustomer->getId())) {
                $message = __("This account cannot be deleted because some orders are still pending. "
                              . "Please complete or cancel all orders before deleting your account.");
                $this->messageManager->addErrorMessage($message);
                return $jsonResult->setHttpResponseCode(400);
            }

            if ($this->getPendingRemovalRequests->getForCustomer($customerId)) {
                $this->messageManager->addErrorMessage(__('Your account already scheduled for data deletion.'));
                return $jsonResult->setHttpResponseCode(400);
            }

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
                        'customer_id'    => (int) $currentCustomer->getId(),
                        'customer_email' => $currentCustomer->getEmail(),
                        'customer_ip'    => $this->remoteAddress->getRemoteAddress(),
                        'website_id'     => $this->storeManager->getStore()->getWebsiteId(),
                    ]
                );
            $this->removalRequestResource->save($removalRequest);
            $this->emailSender->sendRemovalRequestNotification($currentCustomer);
            $this->customerSession->logout();
            return $jsonResult->setData(['errors' => false]);
        } catch (InvalidEmailOrPasswordException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $jsonResult->setHttpResponseCode(401);
        } catch (UserLockedException $e) {
            $this->customerSession->logout();
            $this->customerSession->start();
            $this->messageManager
                ->addErrorMessage(__('You did not sign in correctly or your account is temporarily disabled.'));
            return $jsonResult->setHttpResponseCode(401);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong, please try again later!'));
            return $jsonResult->setHttpResponseCode(500);
        }
    }
}
