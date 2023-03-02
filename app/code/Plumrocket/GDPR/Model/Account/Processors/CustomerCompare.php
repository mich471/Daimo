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

use Magento\Catalog\Helper\Product\Compare;
use Magento\Catalog\Model\ResourceModel\Product\Compare\Item\CollectionFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Plumrocket\GDPR\Api\DataExportProcessorInterface;
use Plumrocket\GDPR\Api\DataProcessorInterface;
use Plumrocket\GDPR\Api\DataRemovalProcessorInterface;

/**
 * Processor customer wishlist.
 *
 * Export and delete customer compare products.
 */
class CustomerCompare implements DataProcessorInterface, DataRemovalProcessorInterface, DataExportProcessorInterface
{
    /**
     * Product compare item collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Compare\Item\CollectionFactory
     */
    private $itemCollectionFactory;

    /**
     * @var \Magento\Catalog\Helper\Product\Compare
     */
    private $compareHelper;

    /**
     * @var array
     */
    private $dataExport;

    /**
     * CustomerWishlist constructor.
     *
     * @param CollectionFactory $itemCollectionFactory
     * @param Compare $compareHelper
     * @param array $dataExport
     */
    public function __construct(
        CollectionFactory $itemCollectionFactory,
        Compare $compareHelper,
        array $dataExport = []
    ) {
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->compareHelper = $compareHelper;
        $this->dataExport = $dataExport;
    }

    /**
     * Executed upon exporting customer data.
     * Expected return structure:
     *      array(
     *          array('HEADER1', 'HEADER2', 'HEADER3', ...),
     *          array('VALUE1', 'VALUE2', 'VALUE3', ...),
     *          ...
     *      )
     *
     * @param CustomerInterface $customer
     * @return array
     * @throws \Exception
     */
    public function export(CustomerInterface $customer)
    {
        return $this->exportCustomerData($customer);
    }

    /**
     * Executed upon customer data deletion.
     *
     * @param CustomerInterface $customer
     * @return void
     * @throws \Exception
     */
    public function delete(CustomerInterface $customer)
    {
        $this->deleteCustomerData($customer);
    }

    /**
     * Executed upon customer data anonymization.
     *
     * @param CustomerInterface $customer
     * @return void
     * @throws \Exception
     */
    public function anonymize(CustomerInterface $customer)
    {
        $this->deleteCustomerData($customer);
    }

    /**
     * @inheritDoc
     */
    public function exportCustomerData(CustomerInterface $customer): ?array
    {
        $compareData = $this->itemCollectionFactory
            ->create()
            ->useProductItem(true)
            ->setCustomerId($customer->getId())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->load();

        if (!$compareData->getSize()) {
            return null;
        }

        $returnData = [];
        $i = 0;
        foreach ($this->dataExport as $title) {
            $returnData[$i][] = $title;
        }

        $i++;

        foreach ($compareData as $item) {
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
        $this->itemCollectionFactory
            ->create()
            ->setCustomerId($customer->getId())
            ->clear();
        $this->compareHelper->setCustomerId($customer->getId());
        $this->compareHelper->calculate();
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteGuestData(string $email): bool
    {
        return false;
    }

    public function getFileName(string $dateTime): string
    {
        return "Compare_Products_$dateTime";
    }
}
