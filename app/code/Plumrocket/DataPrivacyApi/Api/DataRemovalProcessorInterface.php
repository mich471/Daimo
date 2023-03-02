<?php
/**
 * @package     Plumrocket_DataPrivacyApi
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacyApi\Api;

use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Interface for removal processors
 *
 * @since 1.0.0
 */
interface DataRemovalProcessorInterface
{
    /**
     * Executed upon customer data deletion.
     *
     * @param CustomerInterface $customer
     *
     * @return bool
     * @throws \Exception if exception occurred during delete
     */
    public function deleteCustomerData(CustomerInterface $customer): bool;

    /**
     * Remove guest data by email
     *
     * @param string $email
     * @return bool
     * @throws \Exception if exception occurred during delete
     */
    public function deleteGuestData(string $email): bool;
}
