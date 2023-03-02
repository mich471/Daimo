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

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\App\ResourceConnection;
use Magento\Newsletter\Model\Subscriber;
use Magento\Security\Model\ResourceModel\PasswordResetRequestEvent\CollectionFactory as PasswordResetCollectionFactory;
use Plumrocket\GDPR\Api\DataExportProcessorInterface;
use Plumrocket\GDPR\Api\DataProcessorInterface;
use Plumrocket\GDPR\Api\DataRemovalProcessorInterface;

/**
 * Processor customer data.
 *
 * Export and delete customer data.
 * Export and delete guest data.
 */
class CustomerData implements DataProcessorInterface, DataRemovalProcessorInterface, DataExportProcessorInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var PasswordResetCollectionFactory
     */
    private $passwordResetCollectionFactory;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var Subscriber
     */
    private $subscriber;

    /**
     * @var array
     */
    private $dataExport;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    private $customerRegistry;

    /**
     * CustomerData constructor.
     *
     * @param CustomerRepositoryInterface    $customerRepository
     * @param PasswordResetCollectionFactory $passwordResetCollectionFactory
     * @param ResourceConnection             $resourceConnection
     * @param Subscriber                     $subscriber
     * @param CustomerRegistry               $customerRegistry
     * @param array                          $dataExport
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        PasswordResetCollectionFactory $passwordResetCollectionFactory,
        ResourceConnection $resourceConnection,
        Subscriber $subscriber,
        CustomerRegistry $customerRegistry,
        array $dataExport = []
    ) {
        $this->customerRepository = $customerRepository;
        $this->passwordResetCollectionFactory = $passwordResetCollectionFactory;
        $this->resourceConnection = $resourceConnection;
        $this->subscriber = $subscriber;
        $this->dataExport = $dataExport;
        $this->customerRegistry = $customerRegistry;
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
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
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
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return bool|void
     * @throws \Exception
     */
    public function delete(CustomerInterface $customer)
    {
        if ($customer->getId()) {
            return $this->deleteCustomerData($customer);
        }

        return $this->deleteGuestData($customer->getEmail());
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
     * Delete data any table.
     *
     * @param $table
     * @param $idField
     * @param $value
     */
    public function deleteDataFromTable($table, $idField, $value)
    {
        $tableName  = $this->resourceConnection->getTableName($table);
        $connection = $this->resourceConnection->getConnection();
        if ($connection->isTableExists($tableName)) {
            $connection->delete($tableName, [$idField.' = ?' => $value]);
        }
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return array|array[]|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function exportCustomerData(CustomerInterface $customer): ?array
    {
        $customerModel = $this->customerRegistry->retrieve($customer->getId());
        $dataTitles = $dataValues = [];
        $customerData = $customerModel->getData();

        if ($customerModel->getGender()) {
            $genders = [1 => 'Male', 2 => 'Female', 3 => 'Not Specified'];
            $customerData['gender'] = $genders[$customerModel->getGender()];
        }

        $customerData = $this->addNewsletterData($customerModel->getEmail(), $customerData);

        foreach ($this->dataExport as $key => $title) {
            $dataTitles[] = $title;
            $dataValues[] = $customerData[$key] ?? '';
        }

        return [$dataTitles, $dataValues];
    }

    /**
     * @param string $email
     * @return array|array[]|null
     */
    public function exportGuestData(string $email): ?array
    {
        $dataTitles = $dataValues = [];
        $customerData['email'] = $email;
        $customerData = $this->addNewsletterData($email, $customerData);
        foreach ($this->dataExport as $key => $title) {
            $dataTitles[] = $title;
            $dataValues[] = $customerData[$key] ?? '';
        }

        return [$dataTitles, $dataValues];
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteCustomerData(CustomerInterface $customer): bool
    {
        $customerId = $customer->getId();
        $customerEmail = $customer->getEmail();

        $passwordReset = $this->passwordResetCollectionFactory->create()
            ->filterByAccountReference($customerEmail);

        if ($passwordReset->getSize()) {
            $passwordReset->walk('delete');
        }

        $this->deleteDataFromTable('email_contact', 'customer_id', $customerEmail);
        $this->deleteDataFromTable('email_review', 'customer_id', $customerId);
        $this->deleteDataFromTable('email_campaign', 'customer_id', $customerId);
        $this->deleteDataFromTable('email_automation', 'email', $customerEmail);
        $this->customerRepository->deleteById($customerId);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteGuestData(string $email): bool
    {
        $checkSubscriber = $this->subscriber->loadByEmail($email);
        if ($checkSubscriber->isSubscribed()) {
            $this->deleteDataFromTable('newsletter_subscriber', 'subscriber_email', $email);

            return true;
        }

        return false;
    }

    /**
     * @param string $customerEmail
     * @param array  $customerData
     * @return array
     */
    private function addNewsletterData(string $customerEmail, array $customerData): array
    {
        $newsletterSubscribe = 'No';
        $checkSubscriber = $this->subscriber->loadByEmail($customerEmail);
        if ($checkSubscriber->isSubscribed()) {
            $subscriberStatuses = [1 => 'Subscribed', 2 => 'Not Activated', 3 => 'Unsubscribed', 4 => 'Unconfirmed'];
            $newsletterSubscribe = $subscriberStatuses[$checkSubscriber->getStatus()];
        }
        $customerData['newsletter_subscribe'] = $newsletterSubscribe;

        return $customerData;
    }

    /**
     * @inheritDoc
     */
    public function getFileName(string $dateTime): string
    {
        return "Customer_Information_$dateTime";
    }
}
