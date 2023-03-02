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
use Plumrocket\CookieConsent\Api\Data\CookieInterface;

/**
 * @since 1.0.0
 */
interface CookieRepositoryInterface
{
    /**
     * Get info about cookie by name
     *
     * @param string   $name
     * @param int|null $storeId
     * @param bool     $forceReload
     * @return CookieInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(string $name, int $storeId = null, bool $forceReload = false): CookieInterface;

    /**
     * Get category list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Plumrocket\CookieConsent\Api\Data\CookieSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * Get info about cookie by id
     *
     * @param int      $cookieId
     * @param int|null $storeId
     * @param bool     $forceReload
     * @return CookieInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $cookieId, int $storeId = null, bool $forceReload = false): CookieInterface;

    /**
     * Create or update cookie
     *
     * @param \Plumrocket\CookieConsent\Api\Data\CookieInterface $cookie
     * @return \Plumrocket\CookieConsent\Api\Data\CookieInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(CookieInterface $cookie): CookieInterface;

    /**
     * Delete cookie
     *
     * @param \Plumrocket\CookieConsent\Api\Data\CookieInterface $cookie
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\StateException
     */
    public function delete(CookieInterface $cookie): bool;

    /**
     * Delete cookie by id.
     *
     * @param int $id
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function deleteById(int $id): bool;
}
