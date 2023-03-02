<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Controller\Guest;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Plumrocket\DataPrivacy\Controller\AbstractPrivacyCenter;
use Plumrocket\DataPrivacy\Model\Account\Exporter;
use Plumrocket\DataPrivacy\Model\DownloadLogFactory;
use Plumrocket\DataPrivacy\Model\EmailSender;
use Plumrocket\DataPrivacy\Model\Guest\Access\TokenLocator;
use Plumrocket\DataPrivacy\Model\ResourceModel\DownloadLog;
use Plumrocket\Token\Api\CustomerRepositoryInterface as TokenRepositoryInterface;

/**
 * Export customer data.
 *
 * @since 3.1.0
 */
class Export extends AbstractPrivacyCenter
{

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    private $formKeyValidator;

    /**
     * @var \Plumrocket\DataPrivacy\Model\EmailSender
     */
    private $emailSender;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
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
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var TokenRepositoryInterface
     */
    private $tokenRepository;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Plumrocket\DataPrivacy\Model\Account\Exporter
     */
    private $exporter;

    /**
     * @var \Plumrocket\DataPrivacy\Model\Guest\Access\TokenLocator
     */
    private $tokenLocator;

    /**
     * @param \Magento\Framework\App\Action\Context                   $context
     * @param \Plumrocket\DataPrivacy\Model\Guest\Access\Validator    $accessValidator
     * @param \Magento\Customer\Model\Url                             $customerUrl
     * @param \Magento\Framework\App\Http\Context                     $httpContext
     * @param \Plumrocket\DataPrivacy\Model\Guest\Access\TokenLocator $tokenLocator
     * @param \Magento\Framework\Data\Form\FormKey\Validator          $formKeyValidator
     * @param \Plumrocket\DataPrivacy\Model\EmailSender               $emailSender
     * @param \Magento\Customer\Api\CustomerRepositoryInterface       $customerRepository
     * @param \Plumrocket\DataPrivacy\Model\DownloadLogFactory        $logFactory
     * @param \Plumrocket\DataPrivacy\Model\ResourceModel\DownloadLog $logResource
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress    $remoteAddress
     * @param \Magento\Framework\Stdlib\DateTime\DateTime             $dateTime
     * @param \Plumrocket\Token\Api\CustomerRepositoryInterface       $tokenRepository
     * @param \Magento\Store\Model\StoreManagerInterface              $storeManager
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
        DownloadLogFactory $logFactory,
        DownloadLog $logResource,
        RemoteAddress $remoteAddress,
        DateTime $dateTime,
        TokenRepositoryInterface $tokenRepository,
        StoreManagerInterface $storeManager,
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
        $this->tokenRepository = $tokenRepository;
        $this->storeManager = $storeManager;
        $this->exporter = $exporter;
        $this->tokenLocator = $tokenLocator;
    }

    /**
     * Execute export action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (! $this->getRequest()->isPost()
            || ! $this->formKeyValidator->validate($this->getRequest())
            || $this->isLoggedIn()
        ) {
            return $this->resultRedirectFactory->create()->setPath('pr_data_privacy/account/export');
        }

        // Token is always valid, because we validate it earlier in "AbstractGuestPrivacyCenter::dispatch"
        try {
            $guestEmail = $this->tokenRepository->get($this->tokenLocator->getToken())->getEmail();
        } catch (NoSuchEntityException $e) {
            $this->messageManager
                ->addErrorMessage(__('Something went wrong, please try again later!'));
            return $this->resultRedirectFactory->create()->setPath('pr_data_privacy/account/export');
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
            return $this->resultRedirectFactory->create()->setPath('pr_data_privacy/account/export');
        }

        if ($existingCustomer) {
            try {
                $exportLog = $this->logFactory->create()->setData(
                    [
                        'created_at'  => date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp()),
                        'customer_email' => $existingCustomer->getEmail(),
                        'customer_id' => (int) $existingCustomer->getId(),
                        'customer_ip' => $this->remoteAddress->getRemoteAddress()
                    ]
                );
                $this->logResource->save($exportLog);

                $this->emailSender->sendDownloadDataNotification($existingCustomer);
                $this->exporter->exportCustomerData($existingCustomer);
            } catch (\Exception $e) {
                $this->messageManager
                    ->addErrorMessage(__('Something went wrong, please try again later!'));
                return $this->resultRedirectFactory->create()->setPath('pr_data_privacy/account/export');
            }
        } else {
            try {
                $exportLog = $this->logFactory->create()->setData(
                    [
                        'created_at'     => date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp()),
                        'customer_email' => $guestEmail,
                        'customer_id'    => 0,
                        'customer_ip'    => $this->remoteAddress->getRemoteAddress()
                    ]
                );
                $this->logResource->save($exportLog);

                $this->emailSender->sendGuestDownloadDataNotification($guestEmail);
                $this->exporter->exportGuestData($guestEmail);
            } catch (\Exception $e) {
                $this->messageManager
                    ->addErrorMessage(__('Something went wrong, please try again later!'));
                return $this->resultRedirectFactory->create()->setPath('pr_data_privacy/account/export');
            }
        }

        return $this->resultFactory->create(ResultFactory::TYPE_RAW)->setContents('');
    }
}
