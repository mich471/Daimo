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
interface ConsentCheckboxProviderInterface
{
    /**
     * @param int    $customerId
     * @param string $locationKey
     * @return \Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface[]
     */
    public function getCheckboxesToAgreeByLocation(int $customerId, string $locationKey): array;

    /**
     * @param int    $customerId
     * @return \Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface[]
     */
    public function getCheckboxesToAgree(int $customerId): array;

    /**
     * @param int $customerId
     * @return \Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface[]
     */
    public function getEnabledCustomerCheckboxes(int $customerId): array;
}
