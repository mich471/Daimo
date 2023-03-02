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
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Model\Account\Processors\Plumrocket;

use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Plumrocket\DataPrivacyApi\Api\DataExportProcessorInterface;
use Plumrocket\DataPrivacyApi\Api\DataRemovalProcessorInterface;
use Plumrocket\GDPR\Helper\CustomerData;
use Plumrocket\GDPR\Model\ResourceModel\ConsentsLog\CollectionFactory as ConsentsLogCollectionFactory;
use Plumrocket\GDPR\Model\ResourceModel\RemovalRequests\CollectionFactory as RemovalRequestsCollectionFactory;

/**
 * Processor Gdpr.
 */
class Gdpr implements DataRemovalProcessorInterface, DataExportProcessorInterface
{
    /**
     * @var ConsentsLogCollectionFactory
     */
    private $consentsLogCollectionFactory;

    /**
     * @var RemovalRequestsCollectionFactory
     */
    private $removalRequestsCollectionFactory;

    /**
     * @var CustomerData
     */
    protected $customerData;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepositoryInterface;

    /**
     * @var array
     */
    private $dataExport;

    /**
     * @var array
     */
    private $dataAnonymize;

    /**
     * GdprConsentsLog constructor.
     *
     * @param ConsentsLogCollectionFactory $consentsLogCollectionFactory
     * @param RemovalRequestsCollectionFactory $removalRequestsCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Cms\Api\PageRepositoryInterface $pageRepositoryInterface
     * @param array $dataExport
     */
    public function __construct(
        ConsentsLogCollectionFactory $consentsLogCollectionFactory,
        RemovalRequestsCollectionFactory $removalRequestsCollectionFactory,
        CustomerData $customerData,
        StoreManagerInterface $storeManager,
        PageRepositoryInterface $pageRepositoryInterface,
        array $dataExport = [],
        array $dataAnonymize = []
    ) {
        $this->consentsLogCollectionFactory = $consentsLogCollectionFactory;
        $this->removalRequestsCollectionFactory = $removalRequestsCollectionFactory;
        $this->customerData = $customerData;
        $this->storeManager = $storeManager;
        $this->pageRepositoryInterface = $pageRepositoryInterface;
        $this->dataExport = $dataExport;
        $this->dataAnonymize = $dataAnonymize;
    }

    /**
     * We anonymize customer data while changing status
     * @see \Plumrocket\DataPrivacy\Model\Account\Remover::execute
     *
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
     * Executed upon customer data deletion.
     *
     * @param CustomerInterface $customer
     *
     * @return void
     * @throws \Exception
     */
    public function delete(CustomerInterface $customer)// @codingStandardsIgnoreLine
    {
        $this->anonymize($customer);
    }

    /**
     * Executed upon customer data anonymization.
     *
     * @param CustomerInterface $customer
     *
     * @return void
     * @throws \Exception
     */
    public function anonymize(CustomerInterface $customer)// @codingStandardsIgnoreLine
    {
        $customerId = $customer->getId();
        $removalRequests = $this->removalRequestsCollectionFactory->create()
            ->addFieldToFilter('customer_id', ['eq' => $customerId]);

        $dataAnonymized = $this->customerData->getDataAnonymized($this->dataAnonymize, $customerId);
        if (!empty($dataAnonymized) && $removalRequests->getSize()) {
            $removalRequests->setDataToAll($dataAnonymized)->save();
        }
    }

    /**
     * @inheritDoc
     */
    public function exportCustomerData(CustomerInterface $customer): ?array
    {
        $collection = $this->consentsLogCollectionFactory
            ->create()
            ->addFieldToFilter('customer_id', $customer->getId());

        return $this->exportConsentLog($collection);
    }

    /**
     * @inheritDoc
     */
    public function exportGuestData(string $email): ?array
    {
        $collection = $this->consentsLogCollectionFactory
            ->create()
            ->addFieldToFilter('email', $email);

        return $this->exportConsentLog($collection);
    }

    /**
     * @param \Plumrocket\GDPR\Model\ResourceModel\ConsentsLog\Collection $collection
     * @return array|null
     */
    public function exportConsentLog($collection): ?array
    {
        if (! $collection->getSize()) {
            return null;
        }

        $i = 0;
        $returnData = [];
        foreach ($this->dataExport as $title) {
            $returnData[$i][] = $title;
        }

        $i++;

        foreach ($collection as $item) {
            $itemData = $item->getData();

            if (! empty($itemData['cms_page_id'])) {
                try {
                    $itemData['cms_page'] = $this->pageRepositoryInterface->getById(
                        $itemData['cms_page_id']
                    )->getTitle();
                } catch (\Exception $e) {
                    $itemData['cms_page'] = '';
                }
            } else {
                $itemData['cms_page'] = '';
            }

            try {
                $itemData['website'] = $this->storeManager->getWebsite($itemData['website_id'])->getName();
            } catch (\Exception $e) {
                $itemData['website'] = '';
            }

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
    public function getFileName(string $dateTime): string
    {
        return "Consents_Log_$dateTime";
    }
}
