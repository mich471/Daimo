<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model\RemovalRequest;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\User\Api\Data\UserInterface;
use Plumrocket\DataPrivacy\Helper\Config;
use Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterface;
use Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterfaceFactory;
use Plumrocket\DataPrivacyApi\Api\RemovalRequestRepositoryInterface;

/**
 * @since 3.2.0
 */
class CreateByAdmin
{

    /**
     * @var \Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterfaceFactory
     */
    private $removalRequestFactory;

    /**
     * @var \Plumrocket\DataPrivacyApi\Api\RemovalRequestRepositoryInterface
     */
    private $removalRequestRepository;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var \Plumrocket\DataPrivacy\Helper\Config
     */
    private $config;

    /**
     * @param \Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterfaceFactory $removalRequestFactory
     * @param \Plumrocket\DataPrivacyApi\Api\RemovalRequestRepositoryInterface   $removalRequestRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface                  $customerRepository
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress               $remoteAddress
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                        $dateTime
     * @param \Plumrocket\DataPrivacy\Helper\Config                              $config
     */
    public function __construct(
        RemovalRequestInterfaceFactory $removalRequestFactory,
        RemovalRequestRepositoryInterface $removalRequestRepository,
        CustomerRepositoryInterface $customerRepository,
        RemoteAddress $remoteAddress,
        DateTime $dateTime,
        Config $config
    ) {
        $this->removalRequestFactory = $removalRequestFactory;
        $this->removalRequestRepository = $removalRequestRepository;
        $this->customerRepository = $customerRepository;
        $this->remoteAddress = $remoteAddress;
        $this->dateTime = $dateTime;
        $this->config = $config;
    }

    /**
     * Create request id.
     *
     * @param int                                  $customerId
     * @param \Magento\User\Api\Data\UserInterface $user
     * @param string                               $adminComment
     * @return \Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function execute(int $customerId, UserInterface $user, string $adminComment): RemovalRequestInterface
    {
        /** @var RemovalRequestInterface $removalRequest */
        $removalRequest = $this->removalRequestFactory->create();

        $customer = $this->customerRepository->getById($customerId);

        $removalRequest
            ->setAdminId((int) $user->getId())
            ->setAdminComment($adminComment)
            ->setCustomerId((int) $customer->getId())
            ->setGuestEmail($customer->getEmail())
            ->setWebsiteId((int) $customer->getWebsiteId())
            ->setCreatedBy(RemovalRequestInterface::CREATED_BY_ADMIN)
            ->setCreatorIp($this->remoteAddress->getRemoteAddress())
            ->setCreatedAt(date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp()))
            ->setScheduledAt(
                date(
                    'Y-m-d H:i:s',
                    $this->dateTime->gmtTimestamp() + $this->config->getDeletionTime()
                )
            );

        return $this->removalRequestRepository->save($removalRequest);
    }
}
