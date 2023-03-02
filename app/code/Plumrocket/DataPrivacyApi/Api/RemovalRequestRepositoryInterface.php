<?php
/**
 * @package     Plumrocket_DataPrivacyApi
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\DataPrivacyApi\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterface;

/**
 * @since 3.1.0
 */
interface RemovalRequestRepositoryInterface
{

    /**
     * Create removal request.
     *
     * @param \Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterface $removalRequest
     * @return \Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(RemovalRequestInterface $removalRequest): RemovalRequestInterface;

    /**
     * Get info about removal request by its id.
     *
     * @param int  $removalRequestId
     * @param bool $forceReload
     * @return \Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $removalRequestId, bool $forceReload = false): RemovalRequestInterface;

    /**
     * Get removal request list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;
}
