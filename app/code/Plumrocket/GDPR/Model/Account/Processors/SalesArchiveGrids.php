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
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\GDPR\Model\Account\Processors;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\ResourceConnection;
use Plumrocket\GDPR\Api\DataExportProcessorInterface;
use Plumrocket\GDPR\Api\DataProcessorInterface;
use Plumrocket\GDPR\Api\DataRemovalProcessorInterface;
use Plumrocket\GDPR\Helper\CustomerData as CustomerDataHelper;
use Plumrocket\GDPR\Helper\Data;

/**
 * Processor Sales Archive.
 *
 * Only for Magento 2 Enterprise.
 * Delete customer sales archive grids.
 */
class SalesArchiveGrids implements DataProcessorInterface, DataRemovalProcessorInterface, DataExportProcessorInterface
{
    /**
     * @var string
     */
    const ORDER_COLLECTIONS = 'Magento\SalesArchive\Model\ResourceModel\Order\Collection';

    /**
     * @var ArchiveCollection
     */
    private $archiveCollection;

    /**
     * @var array
     */
    private $dataAnonymize;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var CustomerDataHelper
     */
    private $customerData;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * SalesArchiveGrids constructor.
     *
     * @param Data               $helperData
     * @param ResourceConnection $resourceConnection
     * @param CustomerDataHelper $customerData
     * @param array              $dataAnonymize
     */
    public function __construct(
        Data $helperData,
        ResourceConnection $resourceConnection,
        CustomerDataHelper $customerData,
        array $dataAnonymize = []
    ) {
        $this->helperData = $helperData;
        $this->customerData = $customerData;
        $this->resourceConnection = $resourceConnection;
        $this->dataAnonymize = $dataAnonymize;
    }

    /**
     * @return mixed|ArchiveCollection
     */
    public function getCollection()
    {
        if (null === $this->archiveCollection) {
            $this->archiveCollection = $this->helperData->getResourceByName(self::ORDER_COLLECTIONS);
        }

        return $this->archiveCollection;
    }

    /**
     * Executed upon exporting customer Archive data.
     * Expected return structure:
     *      array(
     *          array('HEADER1', 'HEADER2', 'HEADER3', ...),
     *          array('VALUE1', 'VALUE2', 'VALUE3', ...),
     *          ...
     *      )
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return array
     */
    public function export(CustomerInterface $customer)
    {
        if ($customer->getId()) {
            return $this->exportCustomerData($customer);
        }

        return $this->exportGuestData($customer->getEmail());
    }

    /**
     * Executed upon customer data deletion.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return void|bool
     */
    public function delete(CustomerInterface $customer)
    {
        if ($customer->getId()) {
            return $this->deleteCustomerData($customer);
        }

        return $this->deleteGuestData($customer->getEmail());
    }

    /**
     * Executed upon customer Archive data anonymization.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return void|bool
     */
    public function anonymize(CustomerInterface $customer)
    {
        if (! $this->getCollection()) {
            return false;
        }

        $archiveCollection = $this->getCollection()
            ->addFieldToFilter('customer_id', $customer->getId());

        $dataAnonymized = $this->customerData->getDataAnonymized($this->dataAnonymize, $customer->getId());
        $archiveIds = [];

        foreach ($archiveCollection as $archiveItem) {
            $archiveIds[] = $archiveItem->getId();
        }

        if (! empty($archiveIds)) {
            $this->updateDataInTable('magento_sales_shipment_grid_archive', 'order_id', $archiveIds, $dataAnonymized);
            $this->updateDataInTable('magento_sales_order_grid_archive', 'entity_id', $archiveIds, $dataAnonymized);
            unset($dataAnonymized['shipping_name']);
            $this->updateDataInTable('magento_sales_invoice_grid_archive', 'order_id', $archiveIds, $dataAnonymized);
            $this->updateDataInTable('magento_sales_creditmemo_grid_archive', 'order_id', $archiveIds, $dataAnonymized);
        }

        return true;
    }

    /**
     * Update any table.
     *
     * @param $table
     * @param $idField
     * @param $ids
     * @param $data
     */
    private function updateDataInTable($table, $idField, $ids, $data)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName($table);

        $connection->update(
            $tableName,
            $data,
            [$idField . ' IN (?)' => $ids]
        );
    }

    /**
     * @inheritDoc
     */
    public function exportCustomerData(CustomerInterface $customer): ?array
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function exportGuestData(string $email): ?array
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function deleteCustomerData(CustomerInterface $customer): bool
    {
        return $this->anonymize($customer);
    }

    /**
     * @inheritDoc
     */
    public function deleteGuestData(string $email): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getFileName(string $dateTime): string
    {
        return "Magento_SalesArchive_$dateTime";
    }
}
