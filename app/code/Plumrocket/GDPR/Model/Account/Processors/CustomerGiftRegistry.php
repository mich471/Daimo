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
 * Processor GiftRegistry.
 *
 * Only for Magento 2 Commerce.
 * Export and delete customer gifts.
 */
class CustomerGiftRegistry implements
    DataProcessorInterface,
    DataRemovalProcessorInterface,
    DataExportProcessorInterface
{
    /**
     * @var string
     */
    const ENTITY_COLLECTION = 'Magento\GiftRegistry\Model\ResourceModel\Entity\Collection';

    /**
     * @var string
     */
    const PERSON_COLLECTION = 'Magento\GiftRegistry\Model\ResourceModel\Person\Collection';

    /**
     * @var mixed
     */
    private $personCollection;

    /**
     * @var mixed
     */
    private $entityCollection;

    /**
     * @var array
     */
    private $dataExport;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * CustomerGiftRegistry constructor.
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
     * @return mixed
     */
    public function getPersonCollection()
    {
        if (null === $this->personCollection) {
            $this->personCollection = $this->helperData->getResourceByName(self::PERSON_COLLECTION);
        }

        return $this->personCollection;
    }

    /**
     * @return mixed
     */
    public function getEntityCollection()
    {
        if (null === $this->entityCollection) {
            $this->entityCollection = $this->helperData->getResourceByName(self::ENTITY_COLLECTION);
        }

        return $this->entityCollection;
    }

    /**
     * Executed upon exporting customer gift data.
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
     * Executed upon customer gift data deletion.
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
     * Executed upon customer gift data anonymization.
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
        $fields = [
            'firstname',
            'lastname',
            'email',
            'role'
        ];

        if (! $this->getEntityCollection() || ! $this->getPersonCollection()) {
            return [];
        }

        $dataModelItems = $this->getEntityCollection()->addFieldToFilter('customer_id', $customer->getId());

        foreach ($dataModelItems as $item) {
            $personModelItems = $this->getPersonCollection()->addFieldToFilter('entity_id', $item->getId());

            foreach ($personModelItems as $personItem) {

                foreach ($fields as $field) {
                    if (! empty($personItem->getData($field))) {
                        $item->setData($field, $personItem->getData($field));
                    }
                }
            }
        }

        $returnData = [];
        $i = 0;

        if (! $dataModelItems->getSize()) {
            return [];
        }

        foreach ($this->dataExport as $title) {
            $returnData[$i][] = $title;
        }

        $i++;

        foreach ($dataModelItems as $item) {
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

    /**
     * @inheritDoc
     */
    public function deleteCustomerData(CustomerInterface $customer): bool
    {
        if (! $this->getEntityCollection()) {
            return false;
        }

        $collection = $this->getEntityCollection()
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
    public function getFileName(string $dateTime): string
    {
        return "Magento_GiftRegistry_$dateTime";
    }
}
