<?php
/**
 * @package     Plumrocket_DataPrivacyApi
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\DataPrivacyApi\Api\Data;

/**
 * @since 2.0.0
 */
interface CheckboxSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get attributes list.
     *
     * @return \Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface[]
     */
    public function getItems() : array;

    /**
     * Set attributes list.
     *
     * @param \Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface[] $items
     * @return \Plumrocket\DataPrivacyApi\Api\Data\CheckboxSearchResultsInterface
     */
    public function setItems(array $items) : self;
}
