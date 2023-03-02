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
use Plumrocket\GDPR\Api\DataExportProcessorInterface;
use Plumrocket\GDPR\Api\DataProcessorInterface;
use Plumrocket\GDPR\Api\DataRemovalProcessorInterface;
use Plumrocket\GDPR\Helper\Data;

/**
 * Processor Customer Balance.
 *
 * Only for Magento 2 Enterprise.
 * Export and delete customer balance.
 */
class CustomerBalance implements DataProcessorInterface, DataRemovalProcessorInterface, DataExportProcessorInterface
{

    /**
     * @var string
     */
    const BALANCE_COLLECTION = 'Magento\CustomerBalance\Model\ResourceModel\Balance\Collection';

    /**
     * @var array
     */
    private $dataExport;

    /**
     * @var \Magento\CustomerBalance\Model\ResourceModel\Balance\Collection
     */
    private $balanceCollection;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * CustomerBalance constructor.
     *
     * @param Data  $helperData
     * @param array $dataExport
     */
    public function __construct(
        Data $helperData,
        array $dataExport = []
    ) {
        $this->helperData = $helperData;
        $this->dataExport = $dataExport;
    }

    /**
     * @return \Magento\CustomerBalance\Model\ResourceModel\Balance\Collection|null
     */
    public function getCollection()
    {
        if (null === $this->balanceCollection) {
            $this->balanceCollection = $this->helperData->getResourceByName(self::BALANCE_COLLECTION);
        }

        return $this->balanceCollection;
    }

    /**
     * Executed upon exporting customer balance data.
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
        if (! $this->getCollection()) {
            return [];
        }

        return $this->exportCustomerData($customer);
    }

    /**
     * Executed upon balance data deletion.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return bool
     */
    public function delete(CustomerInterface $customer)
    {
        if (! $this->getCollection()) {
            return false;
        }
        return $this->deleteCustomerData($customer);
    }

    /**
     * Executed upon balance data anonymization.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return void|bool
     */
    public function anonymize(CustomerInterface $customer)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function deleteCustomerData(CustomerInterface $customer): bool
    {
        if (! $this->getCollection()) {
            return false;
        }
        $collection = $this->getCollection()
            ->addFieldToFilter('customer_id', $customer->getId());

        if (! $collection->getSize()) {
            return false;
        }

        $collection->walk('delete');
        return true;
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
    public function exportCustomerData(CustomerInterface $customer): ?array
    {
        if (! $this->getCollection()) {
            return [];
        }

        $balanceData = $this->getCollection()
            ->addFieldToFilter('customer_id', ['eq' => $customer->getId()]);

        $returnData = [];
        $i = 0;

        if (! $balanceData->getSize()) {
            return [];
        }

        foreach ($this->dataExport as $key => $title) {
            $returnData[$i][] = $title;
        }

        $i++;

        foreach ($balanceData as $item) {
            $itemData = $item->getData();

            foreach ($this->dataExport as $key => $title) {
                $returnData[$i][] = $itemData[$key] ?? '';
            }

            $i++;
        }

        return $returnData;
    }

    /**
     * @inheritDoc
     */
    public function exportGuestData(string $email): ?array
    {
        return null;
    }

    public function getFileName(string $dateTime): string
    {
        return "Magento_CustomerBalance_$dateTime";
    }
}
