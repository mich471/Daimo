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

namespace Plumrocket\Token\Api\Data;

interface CustomerSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get token list.
     *
     * @return \Plumrocket\Token\Api\Data\CustomerInterface[]
     */
    public function getItems() : array;

    /**
     * Set token list.
     *
     * @param \Plumrocket\Token\Api\Data\CustomerInterface[] $items
     * @return $this
     */
    public function setItems(array $items) : self;
}
