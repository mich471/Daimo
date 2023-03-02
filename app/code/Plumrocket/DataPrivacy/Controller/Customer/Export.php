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
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Plumrocket\DataPrivacy\Controller\AbstractPrivacyCenter;
use Plumrocket\DataPrivacy\Model\Account\Exporter;
use Plumrocket\DataPrivacy\Model\DownloadLogFactory as LogFactory;
use Plumrocket\DataPrivacy\Model\EmailSender;
use Plumrocket\DataPrivacy\Model\Guest\Access\TokenLocator;
use Plumrocket\DataPrivacy\Model\ResourceModel\DownloadLog as LogResource;

/**
 * Export customer data.
 *
 * @since 3.1.0
 */
class Export extends AbstractPrivacyCenter
{
    /**
     * @var Validator
     */
    private $formKeyValidator;

    /**
     * @var \Plumrocket\DataPrivacy\Model\EmailSender
     */
    private $emailSender;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Plumrocket\DataPrivacy\Model\DownloadLogFactory
     */
    private $logFactory;

    /**
     * @var \Plumrocket\DataPrivacy\Model\ResourceModel\DownloadLog
     */
    private $logResource;

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var \Plumrocket\GDPR\Controller\Customer\Magento\Customer\Model\AuthenticationInterface
     */
    private $authentication;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Plumrocket\DataPrivacy\Model\Account\Exporter
     */
    private $exporter;

    /**
     * @param \Magento\Framework\App\Action\Context                   $context
     * @param \Plumrocket\DataPrivacy\Model\Guest\Access\Validator    $accessValidator
     * @param \Magento\Customer\Model\Url                             $customerUrl
     * @param \Magento\Framework\App\Http\Context                     $httpContext
     * @param \Plumrocket\DataPrivacy\Model\Guest\Access\TokenLocator $tokenLocator
     * @param \Magento\Framework\Data\Form\FormKey\Validator          $formKeyValidator
     * @param \Plumrocket\DataPrivacy\Model\EmailSender               $emailSender
     * @param \Magento\Customer\Api\CustomerRepositoryInterface       $customerRepository
     * @param \Magento\Customer\Model\Session                         $customerSession
     * @param \Plumrocket\DataPrivacy\Model\DownloadLogFactory        $logFactory
     * @param \Plumrocket\DataPrivacy\Model\ResourceModel\DownloadLog $logResource
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress    $remoteAddress
     * @param \Magento\Framework\Stdlib\DateTime\DateTime             $dateTime
     * @param \Magento\Customer\Model\AuthenticationInterface         $authentication
     * @param \Plumrocket\DataPrivacy\Model\Account\Exporter          $exporter
     */
    public function __construct(
        Context $context,
        \Plumrocket\DataPrivacy\Model\Guest\Access\Validator $accessValidator,
        CustomerUrl $customerUrl,
        \Magento\Framework\App\Http\Context $httpContext,
        TokenLocator $tokenLocator,
        Validator $formKeyValidator,
        EmailSender $emailSender,
        CustomerRepositoryInterface $customerRepository,
        Session $customerSession,
        LogFactory $logFactory,
        LogResource $logResource,
        RemoteAddress $remoteAddress,
        DateTime $dateTime,
        AuthenticationInterface $authentication,
        Exporter $exporter
    ) {
        parent::__construct($context, $accessValidator, $customerUrl, $httpContext, $tokenLocator);

        $this->formKeyValidator = $formKeyValidator;
        $this->emailSender = $emailSender;
        $this->customerRepository = $customerRepository;
        $this->logFactory = $logFactory;
        $this->logResource = $logResource;
        $this->remoteAddress = $remoteAddress;
        $this->dateTime = $dateTime;
        $this->authentication = $authentication;
        $this->customerSession = $customerSession;
        $this->exporter = $exporter;
    }

    /**
     * Execute export action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (! $this->isLoggedIn()) {
            return $this->resultRedirectFactory->create()->setPath('customer/account/login');
        }

        if (! $this->formKeyValidator->validate($this->getRequest()) && $this->getRequest()->isPost()) {
            return $this->resultRedirectFactory->create()->setPath('pr_data_privacy/account/export');
        }

        try {
            $currentCustomer = $this->customerRepository->getById($this->customerSession->getCustomerId());
            $password = $this->getRequest()->getPost('password');
            $this->authentication->authenticate($currentCustomer->getId(), $password);

            $exportLog = $this->logFactory->create()->setData(
                [
                    'created_at'  => date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp()),
                    'customer_email' => $currentCustomer->getEmail(),
                    'customer_id' => (int) $currentCustomer->getId(),
                    'customer_ip' => $this->remoteAddress->getRemoteAddress()
                ]
            );
            $this->logResource->save($exportLog);

            $this->emailSender->sendDownloadDataNotification($currentCustomer);
            $this->exporter->exportCustomerData($currentCustomer);
        } catch (InvalidEmailOrPasswordException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $this->resultRedirectFactory->create()->setPath('pr_data_privacy/account/export');
        } catch (NoSuchEntityException $e) {
            $this->messageManager
                ->addErrorMessage(__('We cannot find your account. Please try again later.'));
            return $this->resultRedirectFactory->create()->setPath('pr_data_privacy/account/export');
        } catch (\Exception $e) {
            $this->messageManager
                ->addErrorMessage(__('Something went wrong, please try again later!'));
            return $this->resultRedirectFactory->create()->setPath('pr_data_privacy/account/export');
        }

        return $this->resultFactory->create(ResultFactory::TYPE_RAW)->setContents('');
    }
}
