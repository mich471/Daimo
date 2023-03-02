<?php
/**
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Plumrocket\CookieConsent\Api\Data\CategoryInterface;

/**
 * @since 1.0.0
 */
interface CategoryRepositoryInterface
{
    /**
     * Get info about category by category key
     *
     * @param string   $categoryKey
     * @param int|null $storeId
     * @param bool     $forceReload
     * @return CategoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(string $categoryKey, int $storeId = null, bool $forceReload = false): CategoryInterface;

    /**
     * Get category list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Plumrocket\CookieConsent\Api\Data\CategorySearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * Get info about category by category id
     *
     * @param int      $categoryId
     * @param int|null $storeId
     * @param bool     $forceReload
     * @return CategoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $categoryId, int $storeId = null, bool $forceReload = false): CategoryInterface;

    /**
     * Create cookie category
     *
     * @param \Plumrocket\CookieConsent\Api\Data\CategoryInterface $category
     * @return \Plumrocket\CookieConsent\Api\Data\CategoryInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(CategoryInterface $category): CategoryInterface;

    /**
     * Delete category
     *
     * @param \Plumrocket\CookieConsent\Api\Data\CategoryInterface $category
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\StateException
     */
    public function delete(CategoryInterface $category): bool;

    /**
     * Delete category by id.
     *
     * @param int $id
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function deleteById(int $id): bool;
}
