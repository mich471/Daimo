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
interface ConsentCheckboxesValidatorInterface
{
    /**
     * @param array  $checkedConsentCheckboxIds
     * @param string $locationKey
     * @param int    $customerId
     * @return bool
     * @throws \InvalidArgumentException if location is unknown
     */
    public function isAcceptedAllRequiredCheckboxes(
        array $checkedConsentCheckboxIds,
        string $locationKey,
        int $customerId
    ): bool;
}
