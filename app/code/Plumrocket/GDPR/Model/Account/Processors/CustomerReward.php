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
 * Processor Reward.
 *
 * Only for Magento 2 Enterprise.
 * Export and delete customer rewards.
 */
class CustomerReward implements DataProcessorInterface, DataRemovalProcessorInterface, DataExportProcessorInterface
{
    /**
     * @var string
     */
    const REWARD_COLLECTION = 'Magento\Reward\Model\ResourceModel\Reward\Collection';

    /**
     * @var Magento\Reward\Model\ResourceModel\Reward\Collection
     */
    private $rewardCollection;

    /**
     * @var array
     */
    private $dataExport;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * CustomerReward constructor.
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
     * @return null|Magento\Reward\Model\ResourceModel\Reward\Collection
     */
    public function getCollection()
    {
        if (null === $this->rewardCollection) {
            $this->rewardCollection = $this->helperData->getResourceByName(self::REWARD_COLLECTION);
        }

        return $this->rewardCollection;
    }

    /**
     * Executed upon exporting customer Reward data.
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
     * Executed upon customer Reward data deletion.
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
     * Executed upon customer Reward data anonymization.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return void|false
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
            return null;
        }

        $rewardData = $this->getCollection()
            ->addFieldToFilter('customer_id', $customer->getId());

        $returnData = [];
        $i = 0;

        if (! $rewardData->getSize()) {
            return [];
        }

        foreach ($this->dataExport as $key => $title) {
            $returnData[$i][] = $title;
        }

        $i++;

        foreach ($rewardData as $item) {
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
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return bool
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
    public function getFileName(string $dateTime): string
    {
        return "Magento_Reward_$dateTime";
    }
}
