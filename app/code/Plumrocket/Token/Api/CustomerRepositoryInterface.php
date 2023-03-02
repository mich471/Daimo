<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Token
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Token\Api;

use Magento\Framework\Api\SearchResultsInterface;
use Plumrocket\Token\Api\Data\CustomerInterface as TokenDataInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface CustomerRepositoryInterface
{
    /**
     * Create token
     *
     * @param \Plumrocket\Token\Api\Data\CustomerInterface $token
     * @return \Plumrocket\Token\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Plumrocket\Token\Api\Data\CustomerInterface $token) : TokenDataInterface;

    /**
     * Get info about token by token hash
     *
     * @param string $tokenHash
     * @param bool   $forceReload
     * @return \Plumrocket\Token\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(string $tokenHash, bool $forceReload = false) : TokenDataInterface;

    /**
     * Get info about token by token id
     *
     * @param int  $tokenId
     * @param bool $forceReload
     * @return \Plumrocket\Token\Api\Data\CustomerInterface
     */
    public function getById(int $tokenId, bool $forceReload = false) : TokenDataInterface;

    /**
     * Delete token
     *
     * @param \Plumrocket\Token\Api\Data\CustomerInterface $token
     * @return bool
     */
    public function delete(\Plumrocket\Token\Api\Data\CustomerInterface $token) : bool;

    /**
     * @param int $id
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function deleteById(int $id) : bool;

    /**
     * Get token list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Plumrocket\Token\Api\Data\CustomerSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null) : SearchResultsInterface;
}
