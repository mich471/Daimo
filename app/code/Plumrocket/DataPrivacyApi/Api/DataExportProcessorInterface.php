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
 * Interface for export processors
 *
 * @since 1.0.0
 */
interface DataExportProcessorInterface
{
    /**
     * Export customer data
     *
     * Module processors should return structure:
     *      array(
     *          array('HEADER1', 'HEADER2', 'HEADER3', ...),
     *          array('VALUE1', 'VALUE2', 'VALUE3', ...),
     *          ...
     *      )
     *
     * Composite processor should create zip file and give it to customer
     *
     * @param CustomerInterface $customer
     *
     * @return array|null
     * @throws \Exception if exception occurred during collecting data
     */
    public function exportCustomerData(CustomerInterface $customer): ?array;

    /**
     * Export guest data by email
     *
     * @param string $email
     * @return array|null
     * @throws \Exception if exception occurred during collecting data
     */
    public function exportGuestData(string $email): ?array;

    /**
     * Return file name without extension.
     *
     * @param string $dateTime date and time of exporting.
     * @return string
     * @since 2.0.0
     */
    public function getFileName(string $dateTime): string;
}
