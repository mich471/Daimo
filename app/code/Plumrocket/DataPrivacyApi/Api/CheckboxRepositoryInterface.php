<?php
/**
 * @package     Plumrocket_DataPrivacyApi
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\DataPrivacyApi\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface as DataCheckboxInterface;

/**
 * @since 2.0.0
 */
interface CheckboxRepositoryInterface
{
    /**
     * Create product
     *
     * @param \Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface $checkbox
     * @return \Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(DataCheckboxInterface $checkbox) : DataCheckboxInterface;

    /**
     * Get info about checkbox by checkbox id
     *
     * @param int $checkboxId
     * @param int|null $storeId
     * @param bool $forceReload
     * @return \Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($checkboxId, $storeId = null, $forceReload = false) : DataCheckboxInterface;

    /**
     * Delete checkbox
     *
     * @param \Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface $checkbox
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\StateException
     */
    public function delete(DataCheckboxInterface $checkbox) : bool;

    /**
     * @param int $id
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function deleteById($id) : bool;

    /**
     * Get checkbox list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Plumrocket\DataPrivacyApi\Api\Data\CheckboxSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
