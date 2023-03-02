<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model\RemovalRequest;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Plumrocket\DataPrivacy\Model\OptionSource\RemovalStatus;
use Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterface;
use Plumrocket\DataPrivacyApi\Api\RemovalRequestRepositoryInterface;

/**
 * @since 3.2.0
 */
class GetPending
{

    /**
     * @var \Plumrocket\DataPrivacyApi\Api\RemovalRequestRepositoryInterface
     */
    private $removalRequestRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param \Plumrocket\DataPrivacyApi\Api\RemovalRequestRepositoryInterface $removalRequestRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder                     $searchCriteriaBuilder
     */
    public function __construct(
        RemovalRequestRepositoryInterface $removalRequestRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->removalRequestRepository = $removalRequestRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Get all pending removal requests for customer.
     *
     * @param int $customerId
     * @return array
     */
    public function getForCustomer(int $customerId): array
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(RemovalRequestInterface::CUSTOMER_ID, $customerId)
            ->addFilter(RemovalRequestInterface::STATUS, RemovalStatus::PENDING)
            ->create();

        return $this->removalRequestRepository->getList($searchCriteria)->getItems();
    }

    /**
     * Get all pending removal requests for guest.
     *
     * @param string $guestEmail
     * @return array
     */
    public function getForGuest(string $guestEmail): array
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(RemovalRequestInterface::GUEST_EMAIL, $guestEmail)
            ->addFilter(RemovalRequestInterface::STATUS, RemovalStatus::PENDING)
            ->create();

        return $this->removalRequestRepository->getList($searchCriteria)->getItems();
    }
}
