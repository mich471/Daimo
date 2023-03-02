<?php
/**
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @since 1.0.0
 */
interface CookieSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get cookie list.
     *
     * @return \Plumrocket\CookieConsent\Api\Data\CookieInterface[]
     */
    public function getItems() : array;

    /**
     * Set cookie list.
     *
     * @param \Plumrocket\CookieConsent\Api\Data\CookieInterface[] $items
     * @return $this
     */
    public function setItems(array $items) : self;
}
