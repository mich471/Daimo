<?php
/**
 * @package     Plumrocket_DataPrivacyApi
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\DataPrivacyApi\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @since 2.1.0
 */
interface RemovalRequestResultsInterface extends SearchResultsInterface
{
    /**
     * Get removal request list.
     *
     * @return \Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterface[]
     */
    public function getItems() : array;

    /**
     * Set removal request list.
     *
     * @param \Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterface[] $items
     * @return $this
     */
    public function setItems(array $items) : self;
}
