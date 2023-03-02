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
 * Processor Rma Items.
 *
 * Only for Magento 2 Enterprise.
 * Export and delete customer Rma items.
 */
class CustomerRmaItems implements DataProcessorInterface, DataRemovalProcessorInterface, DataExportProcessorInterface
{
    /**
     * @var string
     */
    const RMA = 'Magento\Rma\Model\Rma';

    /**
     * @var string
     */
    const RMA_EAV = 'Magento\Rma\Helper\Eav';

    /**
     * @var array
     */
    private $dataExport;

    /**
     * @var Eav
     */
    private $rmaEav;

    /**
     * @var Rma
     */
    private $rmaCollection;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * CustomerRmaItems constructor.
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
     * @return mixed|Rma
     */
    public function getCollection()
    {
        if (null === $this->rmaCollection) {
            $this->rmaCollection = $this->helperData->getResourceByName(self::RMA);
        }

        return $this->rmaCollection;
    }

    public function getRmaEav()
    {
        if (null === $this->rmaEav) {
            $this->rmaEav = $this->helperData->getResourceByName(self::RMA_EAV);
        }

        return $this->rmaEav;
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
        return false;
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return array|null
     */
    public function exportCustomerData(CustomerInterface $customer): ?array
    {
        if (! $this->getCollection()) {
            return [];
        }

        $rmaData = $this->getCollection()->getCollection()
            ->addFieldToFilter('customer_id', $customer->getId());

        $returnData = [];
        $i = 0;

        if (! $rmaData->getSize()) {
            return [];
        }

        foreach ($this->dataExport as $key => $title) {
            $returnData[$i][] = $title;
        }

        $i++;

        $this->getRmaEav();
        $reason = $this->rmaEav->getAttributeOptionStringValues();

        foreach ($rmaData as $rma) {

            foreach ($rma->getItemsForDisplay()->getItems() as $item) {
                $itemData = $item->getData();
                $itemData['reason'] = $reason[$item->getReason()];
                $itemData['condition'] = $reason[$item->getCondition()];
                $itemData['resolution'] = $reason[$item->getResolution()];
                foreach ($this->dataExport as $key => $title) {
                    $returnData[$i][] = $itemData[$key] ?? '';
                }
                $i++;
            }
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

    /**
     * @inheritDoc
     */
    public function deleteCustomerData(CustomerInterface $customer): bool
    {
        return false;
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
        return "Magento_Rma_Items_$dateTime";
    }
}
