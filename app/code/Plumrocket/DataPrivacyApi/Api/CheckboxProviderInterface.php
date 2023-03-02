<?php
/**
 * @package     Plumrocket_DataPrivacyApi
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\DataPrivacyApi\Api;

/**
 * @since 2.0.0
 */
interface CheckboxProviderInterface
{
    /**
     * @param bool $forceReload
     * @return \Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface[]
     */
    public function getEnabled($forceReload = false) : array;

    /**
     * Load all checkboxes
     *
     * @param bool $forceReload
     * @return \Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface[]
     */
    public function getAll($forceReload = false) : array;

    /**
     * @param string $locationKey
     * @return \Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface[]
     */
    public function getByLocation($locationKey) : array;
}
