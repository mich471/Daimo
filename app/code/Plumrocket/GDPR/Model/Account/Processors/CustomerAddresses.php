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
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\ResourceModel\Address;
use Magento\Directory\Model\CountryFactory;
use Magento\Quote\Api\Data\AddressInterfaceFactory;
use Plumrocket\GDPR\Api\DataExportProcessorInterface;
use Plumrocket\GDPR\Api\DataProcessorInterface;
use Plumrocket\GDPR\Api\DataRemovalProcessorInterface;

/**
 * Processor customer addresses.
 *
 * Export and delete customer addresses.
 */
class CustomerAddresses implements DataProcessorInterface, DataRemovalProcessorInterface, DataExportProcessorInterface
{
    /**
     * @var CountryFactory
     */
    private $countryFactory;

    /**
     * @var array
     */
    private $dataExport;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Address
     */
    private $addressResourceModel;

    /**
     * @param \Magento\Directory\Model\CountryFactory       $countryFactory
     * @param \Magento\Customer\Model\CustomerRegistry      $customerRegistry
     * @param \Magento\Customer\Model\ResourceModel\Address $addressResourceModel
     * @param array                                         $dataExport
     */
    public function __construct(
        CountryFactory $countryFactory,
        CustomerRegistry $customerRegistry,
        Address $addressResourceModel,
        array $dataExport = []
    ) {
        $this->countryFactory = $countryFactory;
        $this->dataExport = $dataExport;
        $this->customerRegistry = $customerRegistry;
        $this->addressResourceModel = $addressResourceModel;
    }

    /**
     * Expected return structure:
     *      array(
     *          array('HEADER1', 'HEADER2', 'HEADER3', ...),
     *          array('VALUE1', 'VALUE2', 'VALUE3', ...),
     *          ...
     *      )
     *
     * @param CustomerInterface $customer
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
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
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function delete(CustomerInterface $customer)
    {
        $this->deleteCustomerData($customer);
    }

    /**
     * Executed upon customer data anonymization.
     *
     * @param CustomerInterface $customer
     *
     * @return bool
     */
    public function anonymize(CustomerInterface $customer)
    {
        return false;
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return array|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function exportCustomerData(CustomerInterface $customer): ?array
    {
        $addresses = $this->customerRegistry->retrieve($customer->getId())->getAddresses();

        $returnData = [];
        $i=0;

        if (!$addresses) {
            return null;
        }

        foreach ($this->dataExport as $key => $title) {
            $returnData[$i][] = $title;
        }

        $i++;

        foreach ($addresses as $address) {
            $addressData = $address->getData();
            $addressData['country_id'] = $this->countryFactory->create()->loadByCode(
                $address->getCountryId()
            )->getName();

            foreach ($this->dataExport as $key => $title) {
                $returnData[$i][] = $addressData[$key] ?? '';
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
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Exception
     */
    public function deleteCustomerData(CustomerInterface $customer): bool
    {
        $addresses = $this->customerRegistry->retrieve($customer->getId())->getAddresses();
        if (! $addresses) {
            return false;
        }

        foreach ($addresses as $address) {
            $this->addressResourceModel->delete($address);
        }

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
        return "Customer_Address_$dateTime";
    }
}
