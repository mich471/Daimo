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
use Magento\Framework\Module\ModuleListInterface;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Plumrocket\GDPR\Api\DataExportProcessorInterface;
use Plumrocket\GDPR\Api\DataProcessorInterface;
use Plumrocket\GDPR\Api\DataRemovalProcessorInterface;
use Plumrocket\GDPR\Helper\CustomerData as CustomerDataHelper;
use Plumrocket\GDPR\Helper\Data;

/**
 * Processor customer orders.
 *
 * Export and delete customer orders.
 * Export and delete guest orders.
 */
class CustomerOrders implements DataProcessorInterface, DataRemovalProcessorInterface, DataExportProcessorInterface
{
    /**
     * @var string
     */
    const ARCHIVED_ORDER_COLLECTIONS = 'Magento\SalesArchive\Model\ResourceModel\Order\Collection';

    /**
     * @var CustomerDataHelper
     */
    private $customerData;

    /**
     * @var OrderCollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @var AddressRenderer
     */
    private $addressRenderer;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var array
     */
    private $dataExport;

    /**
     * @var array
     */
    private $dataAnonymize;

    /**
     * @var array
     */
    private $dataAnonymizeAddresses;

    /**
     * @var array
     */
    private $dataAnonymizeGrids;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * @var Magento\SalesArchive\Model\ResourceModel\Order\Collection
     */
    private $orderArchivedCollection;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    private $moduleList;

    /**
     * CustomerOrders constructor.
     *
     * @param CustomerDataHelper                            $customerData
     * @param Data                                          $helperData
     * @param OrderCollectionFactory                        $orderCollectionFactory
     * @param AddressRenderer                               $addressRenderer
     * @param ResourceConnection                            $resourceConnection
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param array                                         $dataExport
     * @param array                                         $dataAnonymize
     * @param array                                         $dataAnonymizeAddresses
     * @param array                                         $dataAnonymizeGrids
     */
    public function __construct(
        CustomerDataHelper $customerData,
        Data $helperData,
        OrderCollectionFactory $orderCollectionFactory,
        AddressRenderer $addressRenderer,
        ResourceConnection $resourceConnection,
        ModuleListInterface $moduleList,
        array $dataExport = [],
        array $dataAnonymize = [],
        array $dataAnonymizeAddresses = [],
        array $dataAnonymizeGrids = []
    ) {
        $this->helperData = $helperData;
        $this->customerData = $customerData;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->addressRenderer = $addressRenderer;
        $this->resourceConnection     = $resourceConnection;
        $this->dataExport = $dataExport;
        $this->dataAnonymize = $dataAnonymize;
        $this->dataAnonymizeAddresses = $dataAnonymizeAddresses;
        $this->dataAnonymizeGrids = $dataAnonymizeGrids;
        $this->moduleList = $moduleList;
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
     * Returns order data.
     *
     * @param $orderCollection
     * @return array|null
     */
    public function getOrders($orderCollection)
    {
        $returnData = [];
        $i=0;

        if (!$orderCollection->getSize()) {
            return null;
        }

        foreach ($this->dataExport as $title) {
            $returnData[$i][] = $title;
        }
        $i++;

        foreach ($orderCollection as $order) {
            /** @var \Magento\Sales\Model\Order $order */
            $order->setData('increment_id', '#'.$order->getIncrementId());
            $order->setData('billing_adddress', $this->addressRenderer->format($order->getBillingAddress(), 'text'));
            $shipping_adddress = $order->getShippingAddress();
            $order->setData(
                'shipping_adddress',
                (($shipping_adddress) ? $this->addressRenderer->format($shipping_adddress, 'text') : '')
            );
            $order->setData(
                'payment_method',
                $order->getPayment()->getMethodInstance()->getTitle()
            );
            $order->setData(
                'grand_total',
                $order->getOrderCurrency()->formatPrecision($order->getGrandTotal(), 2, [], false)
            );

            $orderData = $order->getData();
            foreach ($this->dataExport as $key => $title) {
                $returnData[$i][] = ($orderData[$key] ?? '');
            }
            $i++;
        }

        return $returnData;
    }

    /**
     * Executed upon customer orders deletion.
     *
     * @param CustomerInterface $customer
     *
     * @return void|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function delete(CustomerInterface $customer)
    {
        if ($customer->getId()) {
            return $this->deleteCustomerData($customer);
        }

        return $this->deleteGuestData($customer->getEmail());
    }

    /**
     * Executed upon customer orders anonymization.
     *
     * @param CustomerInterface $customer
     * @return void|bool
     */
    public function anonymize(CustomerInterface $customer)
    {
        return false;
    }

    /**
     * Process orders.
     *
     * @param string $customerEmail
     * @param int    $customerId
     * @return bool
     */
    protected function processOrders($customerEmail, int $customerId = 0)
    {
        $orderCollection = $this->orderCollectionFactory->create()
            ->addFieldToFilter('customer_email', $customerEmail);

        if (! $orderCollection->getTotalCount()) {
            return false;
        }

        $dataAnonymized = $this->customerData->getDataAnonymized($this->dataAnonymize, $customerId);

        if (!empty($dataAnonymized) && $orderCollection->getSize()) {
            $orderCollection->setDataToAll($dataAnonymized)->save();
        }

        $dataAnonymizeAddresses = $this->customerData->getDataAnonymized($this->dataAnonymizeAddresses, $customerId);
        $orderIds = [];

        foreach ($orderCollection as $order) {
            $orderIds[] = $order->getId();
            $addressesCollection = $order->getAddressesCollection();

            if (!empty($dataAnonymizeAddresses) && $addressesCollection->getSize()) {
                $addressesCollection->setDataToAll($dataAnonymizeAddresses)->save();
            }
        }

        if (!empty($orderIds)) {
            //anonymize data in grids
            $dataAnonymizeGrids = $this->customerData->getDataAnonymized($this->dataAnonymizeGrids, $customerId);
            $this->updateDataInTable('sales_order_grid', 'entity_id', $orderIds, $dataAnonymizeGrids);
            $this->updateDataInTable('sales_shipment_grid', 'order_id', $orderIds, $dataAnonymizeGrids);

            unset($dataAnonymizeGrids['shipping_name']);
            $this->updateDataInTable('sales_invoice_grid', 'order_id', $orderIds, $dataAnonymizeGrids);
            $this->updateDataInTable('sales_creditmemo_grid', 'order_id', $orderIds, $dataAnonymizeGrids);

            $this->clearOrderGrid($customerEmail);
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
    public function updateDataInTable($table, $idField, $ids, $data)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName  = $this->resourceConnection->getTableName($table);

        $connection->update(
            $tableName,
            $data,
            [$idField . ' IN (?)' => $ids]
        );
    }

    /**
     * @param $customerEmail
     * @return void
     */
    private function clearOrderGrid($customerEmail)
    {
        $archivedOrderIds = [];

        if ($this->getOrderArchivedCollection()) {
            $customerOrderCollections = $this->getOrderArchivedCollection()
                ->addFieldToFilter('customer_email', $customerEmail);

            foreach ($customerOrderCollections as $order) {
                $archivedOrderIds[] = $order->getId();
            }
        }

        if (count($archivedOrderIds) > 0) {
            $this->deleteDataInSalesOrderGridTable($archivedOrderIds);
        }
    }

    /**
     * @return bool|\Magento\SalesArchive\Model\ResourceModel\Order\Collection
     */
    private function getOrderArchivedCollection()
    {
        if ($this->moduleList->has('Magento_SalesArchive')) {
            if (null === $this->orderArchivedCollection) {
                $this->orderArchivedCollection = $this->helperData->getResourceByName(self::ARCHIVED_ORDER_COLLECTIONS);
            }
        } else {
            $this->orderArchivedCollection = false;
        }

        return $this->orderArchivedCollection;
    }

    /**
     * @param $ids
     */
    private function deleteDataInSalesOrderGridTable($ids)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName  = $this->resourceConnection->getTableName('sales_order_grid');

        $connection->delete(
            $tableName,
            ['entity_id IN (?)' => $ids]
        );
    }

    /**
     * @inheritDoc
     */
    public function exportCustomerData(CustomerInterface $customer): ?array
    {
        $orderCollection = $this->orderCollectionFactory->create()
            ->addFieldToFilter('customer_email', $customer->getEmail());

        return $this->getOrders($orderCollection);
    }

    /**
     * @inheritDoc
     */
    public function exportGuestData(string $email): ?array
    {
        $orderCollection = $this->orderCollectionFactory->create()
            ->addFieldToFilter('customer_email', $email);

        return $this->getOrders($orderCollection);
    }

    /**
     * @inheritDoc
     */
    public function deleteCustomerData(CustomerInterface $customer): bool
    {
        return $this->processOrders($customer->getEmail(), (int) $customer->getId());
    }

    /**
     * @inheritDoc
     */
    public function deleteGuestData(string $email): bool
    {
        return $this->processOrders($email);
    }

    /**
     * @inheritDoc
     */
    public function getFileName(string $dateTime): string
    {
        return "Order_Information_$dateTime";
    }
}
