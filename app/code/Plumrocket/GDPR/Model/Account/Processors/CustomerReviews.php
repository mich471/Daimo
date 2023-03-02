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
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory;
use Magento\Review\Model\ReviewFactory;
use Plumrocket\GDPR\Api\DataExportProcessorInterface;
use Plumrocket\GDPR\Api\DataProcessorInterface;
use Plumrocket\GDPR\Api\DataRemovalProcessorInterface;
use Plumrocket\GDPR\Helper\CustomerData as CustomerDataHelper;

/**
 * Processor customer reviews.
 *
 * Export and delete customer reviews.
 */
class CustomerReviews implements DataProcessorInterface, DataRemovalProcessorInterface, DataExportProcessorInterface
{
    /**
     * @var ReviewFactory
     */
    private $reviewFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var CustomerDataHelper
     */
    private $customerData;

    /**
     * @var array
     */
    private $dataExport;

    /**
     * CustomerReviews constructor.
     *
     * @param ReviewFactory $reviewFactory
     * @param CollectionFactory $collectionFactory
     * @param ProductRepositoryInterface $productRepository
     * @param CustomerDataHelper $customerData
     * @param array $dataExport
     */
    public function __construct(
        ReviewFactory $reviewFactory,
        CollectionFactory $collectionFactory,
        ProductRepositoryInterface $productRepository,
        CustomerDataHelper $customerData,
        array $dataExport = []
    ) {
        $this->reviewFactory = $reviewFactory;
        $this->collectionFactory = $collectionFactory;
        $this->productRepository = $productRepository;
        $this->customerData = $customerData;
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
     *
     * @return array|null
     */
    public function export(CustomerInterface $customer)
    {
        if ($customer->getId()) {
            return $this->exportCustomerData($customer);
        }

        return $this->exportGuestData($customer->getEmail());
    }

    /**
     * Get review product name.
     *
     * @param int $reviewId
     * @return null|string
     */
    private function getReviewProduct($reviewId)
    {
        $review = $this->reviewFactory->create();
        $review->getResource()->load($review, $reviewId);

        try {
            $product = $this->productRepository->getById($review->getEntityPkValue());
        } catch (NoSuchEntityException $e) {
            return null;
        }

        return $product->getName() . ' (' . $product->getSku() . ')';
    }

    /**
     * Executed upon customer data deletion.
     *
     * @param CustomerInterface $customer
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
     * Executed upon customer data anonymization.
     *
     * @param CustomerInterface $customer
     * @return void
     */
    public function anonymize(CustomerInterface $customer)
    {
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('customer_id', $customer->getId());

        if (!$collection->getSize()) {
            return;
        }

        foreach ($collection as $review) {
            /** @var \Magento\Review\Model\Review $review */
            $review->setData('nickname', $this->customerData->getAnonymousString($customer->getId()));
        }

        $collection->walk('save');
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return array|null
     */
    public function exportCustomerData(CustomerInterface $customer): ?array
    {
        $collection = $this->collectionFactory->create()->addFieldToFilter('detail.customer_id', $customer->getId());
        $returnData = [];
        $i=0;

        if (!$collection->getSize()) {
            return null;
        }

        foreach ($this->dataExport as $key => $title) {
            $returnData[$i][] = $title;
        }

        $i++;

        foreach ($collection as $review) {
            $review->setData('product', $this->getReviewProduct($review->getId()));

            $reviewData = $review->getData();
            foreach ($this->dataExport as $key => $title) {
                $returnData[$i][] = ($reviewData[$key] ?? '');
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
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('customer_id', $customer->getId());

        if (!$collection->getSize()) {
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
        return "Reviews_$dateTime";
    }
}
