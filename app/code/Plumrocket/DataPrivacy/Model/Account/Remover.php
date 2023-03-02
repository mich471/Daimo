<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model\Account;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Plumrocket\DataPrivacy\Model\Account\Data\Anonymizer;
use Plumrocket\DataPrivacy\Model\OptionSource\RemovalStatus;
use Plumrocket\DataPrivacy\Model\RemovalRequest;
use Plumrocket\GDPR\Model\Account\Processor;
use Psr\Log\LoggerInterface;

/**
 * @since 3.1.0
 */
class Remover
{
    /**
     * @var \Plumrocket\DataPrivacy\Model\Account\Data\Anonymizer
     */
    private $anonymizer;

    /**
     * @var \Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest
     */
    private $removalRequestResource;

    /**
     * @var \Plumrocket\DataPrivacy\Model\Account\Validator
     */
    private $validator;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Plumrocket\GDPR\Model\Account\Processor
     */
    private $accountProcessor;

    /**
     * @param \Plumrocket\DataPrivacy\Model\Account\Data\Anonymizer      $anonymizer
     * @param \Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest $removalRequestResource
     * @param \Plumrocket\DataPrivacy\Model\Account\Validator            $validator
     * @param \Psr\Log\LoggerInterface                                   $logger
     * @param \Magento\Customer\Api\CustomerRepositoryInterface          $customerRepository
     * @param \Plumrocket\GDPR\Model\Account\Processor                   $accountProcessor
     */
    public function __construct(
        Anonymizer $anonymizer,
        \Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest $removalRequestResource,
        Validator $validator,
        LoggerInterface $logger,
        CustomerRepositoryInterface $customerRepository,
        Processor $accountProcessor
    ) {
        $this->anonymizer = $anonymizer;
        $this->removalRequestResource = $removalRequestResource;
        $this->validator = $validator;
        $this->logger = $logger;
        $this->customerRepository = $customerRepository;
        $this->accountProcessor = $accountProcessor;
    }

    /**
     * Remove customer/guest and his personal data.
     *
     * @param \Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterface $removalRequest
     * @return bool
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(RemovalRequest $removalRequest): bool
    {
        $customerId = (int) $removalRequest->getCustomerId();
        $guestEmail = (string) $removalRequest->getCustomerEmail();
        if (! $customerId && ! $guestEmail) {
            throw new LocalizedException(
                __(
                    'Cannot process removal request without customer id and email, request id "%1"',
                    $removalRequest->getId()
                )
            );
        }

        if ($customerId) {
            $result = $this->removeCustomer($customerId);
        } else {
            $result = $this->removeGuest($guestEmail);
        }

        if ($result) {
            $removalRequest->addData(
                [
                    'customer_ip'    => $this->anonymizer->getString($customerId),
                    'customer_email' => $this->anonymizer->getEmail($customerId),
                    'status'         => RemovalStatus::COMPLETED,
                ]
            );
            $this->removalRequestResource->save($removalRequest);
        }

        return $result;
    }

    /**
     * Remove customer and his personal data.
     *
     * @param int $customerId
     * @return bool
     * @throws \Exception
     */
    public function removeCustomer(int $customerId): bool
    {
        if ($this->validator->hasCustomerOpenedOrders($customerId)) {
            $this->logger->error(__("This customer [%1] has opened orders.", $customerId));
            return false;
        }

        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException | LocalizedException $e) {
            return false;
        }

        $this->accountProcessor->deleteCustomerData($customer);
        return true;
    }

    /**
     * Remove guest personal data.
     *
     * @param string $email
     * @return bool
     * @throws \Exception
     */
    public function removeGuest(string $email): bool
    {
        if ($this->validator->hasGuestOpenedOrders($email)) {
            $this->logger->error(__("This guest [%1] has opened orders.", $email));
            return false;
        }

        return $this->accountProcessor->deleteGuestData($email);
    }
}
