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
use Plumrocket\GDPR\Helper\CustomerData as CustomerDataHelper;
use Plumrocket\GDPR\Helper\Data;

/**
 * Processor Customer Rma.
 *
 * Only for Magento 2 Enterprise.
 * Export and delete customer Rma.
 */
class CustomerRma implements DataProcessorInterface, DataRemovalProcessorInterface, DataExportProcessorInterface
{
    /**
     * @var string
     */
    const GRID_COLLECTION = 'Magento\Rma\Model\ResourceModel\Grid\Collection';

    /**
     * @var string
     */
    const RMA_COLLECTION = 'Magento\Rma\Model\ResourceModel\Rma\Collection';

    /**
     * @var Magento\Rma\Model\ResourceModel\Grid\Collection
     */
    private $gridCollection;

    /**
     * @var array
     */
    private $dataExport;

    /**
     * @var CustomerDataHelper
     */
    private $customerData;

    /**
     * @var array
     */
    private $dataAnonymize;

    /**
     * @var Magento\Rma\Model\ResourceModel\Rma\Collection
     */
    private $rmaCollection;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * CustomerRma constructor.
     *
     * @param Data               $helperData
     * @param CustomerDataHelper $customerData
     * @param array              $dataExport
     * @param array              $dataAnonymize
     */
    public function __construct(
        Data $helperData,
        CustomerDataHelper $customerData,
        array $dataExport = [],
        array $dataAnonymize = []
    ) {
        $this->helperData = $helperData;
        $this->dataExport = $dataExport;
        $this->customerData = $customerData;
        $this->dataAnonymize = $dataAnonymize;
    }

    /**
     * @return mixed|Magento\Rma\Model\ResourceModel\Rma\Collection
     */
    public function getRmaCollection()
    {
        if (null === $this->rmaCollection) {
            $this->rmaCollection = $this->helperData->getResourceByName(self::RMA_COLLECTION);
        }

        return $this->rmaCollection;
    }

    /**
     * @return mixed|Magento\Rma\Model\ResourceModel\Grid\Collection
     */
    public function getGridCollection()
    {
        if (null === $this->gridCollection) {
            $this->gridCollection = $this->helperData->getResourceByName(self::GRID_COLLECTION);
        }

        return $this->gridCollection;
    }

    /**
     * Executed upon exporting customer Rma data.
     * Expected return structure:
     *      array(
     *          array('HEADER1', 'HEADER2', 'HEADER3', ...),
     *          array('VALUE1', 'VALUE2', 'VALUE3', ...),
     *          ...
     *      )
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return array|bool
     */
    public function export(CustomerInterface $customer)
    {
        if ($customer->getId()) {
            return $this->exportCustomerData($customer);
        }

        return $this->exportGuestData($customer->getEmail());
    }

    /**
     * Executed upon customer Rma data deletion.
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
     * Executed upon customer Rma data anonymization.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return void|bool
     */
    public function anonymize(CustomerInterface $customer)
    {
        foreach ([$this->getGridCollection(), $this->getRmaCollection()] as $model) {
            if (! $model) {
                return false;
            }

            $customerId = $customer->getId();
            $rma = $model->addFieldToFilter('customer_id', ['eq' => $customerId]);
            $this->dataAnonymize['customer_custom_email'] = $this->customerData->getAnonymousString($customerId);

            $dataAnonymized = $this->customerData->getDataAnonymized($this->dataAnonymize, $customerId);

            if (! empty($dataAnonymized) && $rma->getSize()) {
                $rma->setDataToAll($dataAnonymized)->save();
            }
        }

        return true;
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return array|null
     */
    public function exportCustomerData(CustomerInterface $customer): ?array
    {
        if (! $this->getRmaCollection()) {
            return null;
        }

        $rmaData = $this->getRmaCollection()
            ->addFieldToFilter('customer_id', ['eq' => $customer->getId()]);

        $returnData = [];
        $i = 0;

        if (! $rmaData->getSize()) {
            return null;
        }

        foreach ($this->dataExport as $title) {
            $returnData[$i][] = $title;
        }

        $i++;

        foreach ($rmaData as $item) {
            $itemData = $item->getData();

            foreach ($this->dataExport as $key => $title) {
                $returnData[$i][] = $itemData[$key] ?? '';
            }

            $i++;
        }

        return $returnData;
    }

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
        return "Magento_Rma_$dateTime";
    }
}
