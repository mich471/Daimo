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
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Plumrocket\GDPR\Api\DataExportProcessorInterface;
use Plumrocket\GDPR\Api\DataProcessorInterface;
use Plumrocket\GDPR\Api\DataRemovalProcessorInterface;

/**
 * Processor customer order items.
 *
 * Export and delete customer order items.
 * Export and delete guest order items.
 */
class CustomerOrderItems implements DataProcessorInterface, DataRemovalProcessorInterface, DataExportProcessorInterface
{
    /**
     * @var OrderCollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @var array
     */
    private $dataExport;

    /**
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param array                                                      $dataExport
     */
    public function __construct(
        OrderCollectionFactory $orderCollectionFactory,
        array $dataExport = []
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->dataExport = $dataExport;
    }

    /**
     * Executed upon exporting customer data.
     *
     * Expected return structure:
     *      array(
     *          array('HEADER1', 'HEADER2', 'HEADER3', ...),
     *          array('VALUE1', 'VALUE2', 'VALUE3', ...),
     *          ...
     *      )
     *
     * @param CustomerInterface $customer
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
     * Executed upon order items deletion.
     *
     * @param CustomerInterface $customer
     * @return void|bool
     */
    public function delete(CustomerInterface $customer)
    {
        return false;
    }

    /**
     * Executed upon order items anonymization.
     *
     * @param CustomerInterface $customer
     * @return void|bool
     */
    public function anonymize(CustomerInterface $customer)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function exportCustomerData(CustomerInterface $customer): ?array
    {
        $orderCollection = $this->orderCollectionFactory->create()
            ->addFieldToFilter('customer_email', $customer->getEmail());

        return $this->getOrderItems($orderCollection);
    }

    /**
     * @inheritDoc
     */
    public function exportGuestData(string $email): ?array
    {
        $orderCollection = $this->orderCollectionFactory->create()
            ->addFieldToFilter('customer_email', $email);

        return $this->getOrderItems($orderCollection);
    }

    /**
     * Returns order items data.
     *
     * @param $orderCollection
     * @return array|null
     */
    public function getOrderItems($orderCollection)
    {
        $returnData = [];
        $i = 0;

        if (! $orderCollection->getSize()) {
            return null;
        }

        foreach ($this->dataExport as $title) {
            $returnData[$i][] = $title;
        }

        $i++;

        foreach ($orderCollection as $order) {
            $orderItems = $order->getAllVisibleItems();

            foreach ($orderItems as $item) {
                $item->setData('increment_id', '#'.$order->getIncrementId());
                $item->setData('price', $order->getOrderCurrency()->formatPrecision($item->getPrice(), 2, [], false));
                $item->setData(
                    'row_total',
                    $order->getOrderCurrency()->formatPrecision($item->getRowTotal(), 2, [], false)
                );

                switch ($item->getProductType()) {
                    case 'configurable':
                        $product_options = $item->getProductOptions();
                        $product_options_combine = [];

                        foreach ($product_options['attributes_info'] as $attribute_info) {
                            $product_options_combine[] = $attribute_info['label'].": ".$attribute_info['value'];
                        }

                        $item->setData('product_options', implode(", ", $product_options_combine));
                        break;
                    case 'bundle':
                        $product_options = $item->getProductOptions();
                        $product_options_combine = [];

                        foreach ($product_options['bundle_options'] as $bundle_info) {
                            $bundle_price = $order->getOrderCurrency()->formatPrecision(
                                $bundle_info['value'][0]['price'],
                                2,
                                [],
                                false
                            );
                            $product_options_combine[] = $bundle_info['value'][0]['qty']
                                . "x "
                                . $bundle_info['value'][0]['title']
                                . " " . $bundle_price;
                        }

                        $item->setData('product_options', implode(", ", $product_options_combine));
                        break;
                    default:
                        $item->setData('product_options', '');
                        break;
                }

                $itemData = $item->getData();

                foreach ($this->dataExport as $key => $title) {
                    $returnData[$i][] = ($itemData[$key] ?? '');
                }

                $i++;
            }
        }

        return $returnData;
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
        return "Order_Items_$dateTime";
    }
}
