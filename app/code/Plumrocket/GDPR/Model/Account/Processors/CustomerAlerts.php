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

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\ProductAlert\Model\ResourceModel\Price\CollectionFactory as PriceCollectionFactory;
use Magento\ProductAlert\Model\ResourceModel\Stock\CollectionFactory as StockCollectionFactory;
use Plumrocket\GDPR\Api\DataExportProcessorInterface;
use Plumrocket\GDPR\Api\DataProcessorInterface;
use Plumrocket\GDPR\Api\DataRemovalProcessorInterface;

/**
 * Processor customer alerts.
 *
 * Export and delete stock and price alert notifications.
 */
class CustomerAlerts implements DataProcessorInterface, DataRemovalProcessorInterface, DataExportProcessorInterface
{
    /**
     * @var PriceCollectionFactory
     */
    private $priceCollectionFactory;

    /**
     * @var StockCollectionFactory
     */
    private $stockCollectionFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var array
     */
    private $dataExport;

    /**
     * CustomerWishlist constructor.
     *
     * @param PriceCollectionFactory $priceCollectionFactory
     * @param StockCollectionFactory $stockCollectionFactory
     * @param ProductRepositoryInterface $productRepository
     * @param array $dataExport
     */
    public function __construct(
        PriceCollectionFactory $priceCollectionFactory,
        StockCollectionFactory $stockCollectionFactory,
        ProductRepositoryInterface $productRepository,
        array $dataExport = []
    ) {
        $this->priceCollectionFactory = $priceCollectionFactory;
        $this->stockCollectionFactory = $stockCollectionFactory;
        $this->productRepository = $productRepository;
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
     * @throws \Exception
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
     * @param CustomerInterface $customer
     *
     * @return bool
     */
    public function delete(CustomerInterface $customer)
    {
        return false;
    }

    /**
     * Executed upon customer data anonymization.
     *
     * @param CustomerInterface $customer
     *
     * @return void|bool
     */
    public function anonymize(CustomerInterface $customer)
    {
        return false;
    }

    public function exportCustomerData(CustomerInterface $customer): ?array
    {
        $priceCollection = $this->priceCollectionFactory
            ->create()
            ->addFieldToFilter('customer_id', $customer->getId())
            ->load();

        $stockCollection = $this->stockCollectionFactory
            ->create()
            ->addFieldToFilter('customer_id', $customer->getId())
            ->load();

        $returnData = [];
        $i=0;

        if (!$priceCollection->getSize() && !$stockCollection->getSize()) {
            return null;
        }

        foreach ($this->dataExport as $key => $title) {
            $returnData[$i][] = $title;
        }

        $i++;

        if ($priceCollection->getSize()) {
            foreach ($priceCollection as $priceAlert) {
                $priceAlert->setData('type', 'Price Alert');
                $product = $this->productRepository->getById($priceAlert->getProductId());
                $priceAlertData = $this->addProductData($priceAlert, $product);
                foreach ($this->dataExport as $key => $title) {
                    $returnData[$i][] = ($priceAlertData[$key] ?? '');
                }
                $i++;
            }
        }

        if ($stockCollection->getSize()) {
            foreach ($stockCollection as $stockAlert) {
                $stockAlert->setData('type', 'Stock Alert');
                $product = $this->productRepository->getById($stockAlert->getProductId());
                $stockAlertData = $this->addProductData($stockAlert, $product);
                foreach ($this->dataExport as $key => $title) {
                    $returnData[$i][] = ($stockAlertData[$key] ?? '');
                }
                $i++;
            }
        }

        return $returnData;
    }

    /**
     * @param $alert
     * @param $product
     * @return mixed
     */
    private function addProductData($alert, $product)
    {
        $alert->setData('product_name', $product->getName());
        $alert->setData('product_sku', $product->getSku());
        return $alert->getData();
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

    public function getFileName(string $dateTime): string
    {
        return "Price_and_Stock_Alerts_$dateTime";
    }
}
