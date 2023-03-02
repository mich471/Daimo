<?php
/**
 * @package     Plumrocket_DataPrivacyApi
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacyApi\Api;

/**
 * Check if checkbox or related policy was agreed by customer.
 *
 * @since 2.0.0
 */
interface IsAlreadyCheckedCheckboxInterface
{

    public function execute(Data\CheckboxInterface $checkbox, int $customerId = 0, $checkVersion = true): bool;
}
