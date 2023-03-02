<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model\RemovalRequest;

use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest;
use Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest\CollectionFactory;
use Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterface;
use Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterfaceFactory;
use Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestResultsInterfaceFactory;
use Plumrocket\DataPrivacyApi\Api\RemovalRequestRepositoryInterface;

/**
 * @since 1.0.0
 */
class Repository implements RemovalRequestRepositoryInterface
{

    /**
     * @var \Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterfaceFactory
     */
    private $removalRequestFactory;

    /**
     * @var \Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest
     */
    private $removalRequestResource;

    /**
     * @var \Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterface[]
     */
    private $instancesById = [];

    /**
     * @var \Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var \Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @param \Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterfaceFactory           $removalRequestFactory
     * @param \Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest                   $removalRequestResource
     * @param \Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface           $collectionProcessor
     * @param \Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestResultsInterfaceFactory    $searchResultsFactory
     */
    public function __construct(
        RemovalRequestInterfaceFactory $removalRequestFactory,
        RemovalRequest $removalRequestResource,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        RemovalRequestResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->removalRequestFactory = $removalRequestFactory;
        $this->removalRequestResource = $removalRequestResource;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(RemovalRequestInterface $removalRequest): RemovalRequestInterface
    {
        try {
            unset($this->instancesById[$removalRequest->getId()]);
            $this->removalRequestResource->save($removalRequest);
        } catch (Exception $e) {
            throw new CouldNotSaveException(
                __('The removal request was unable to be saved. Please try again.'),
                $e
            );
        }
        return $this->getById((int) $removalRequest->getId(), true);
    }

    /**
     * @inheritDoc
     */
    public function getById(int $removalRequestId, bool $forceReload = false): RemovalRequestInterface
    {
        if (! isset($this->instancesById[$removalRequestId]) || $forceReload) {
            /** @var RemovalRequestInterface|\Plumrocket\DataPrivacy\Model\RemovalRequest $removalRequest */
            $removalRequest = $this->removalRequestFactory->create();
            $this->removalRequestResource->load($removalRequest, $removalRequestId);
            if (! $removalRequest->getId()) {
                throw NoSuchEntityException::singleField(RemovalRequest::ID_FIELD_NAME, $removalRequestId);
            }
            $this->instancesById[$removalRequest->getId()] = $removalRequest;
        }

        return $this->instancesById[$removalRequestId];
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        /** @var \Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest\Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var \Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestResultsInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }
}
