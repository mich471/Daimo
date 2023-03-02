<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model\Account;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/**
 * @since 3.1.0
 */
class Validator
{

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     */
    public function __construct(CollectionFactory $orderCollectionFactory)
    {
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function hasGuestOpenedOrders(string $email): bool
    {
        $orderCollection = $this->orderCollectionFactory
            ->create()
            ->addFieldToFilter('customer_email', $email)
            ->addFieldToFilter(
                'state',
                ['nin' => ['canceled', 'closed', 'complete']]
            );

        return (bool) $orderCollection->getTotalCount();
    }

    /**
     * @param int $customerId
     * @return bool
     */
    public function hasCustomerOpenedOrders(int $customerId): bool
    {
        $orderCollection = $this->orderCollectionFactory
            ->create($customerId)
            ->addFieldToFilter(
                'state',
                ['nin' => ['canceled', 'closed', 'complete']]
            );

        return (bool) $orderCollection->getTotalCount();
    }

    /**
     * @param string $customerEmail
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function hasOpenedOrders(string $customerEmail): bool
    {
        if (! $customerEmail) {
            throw new LocalizedException(
                __('Error. Customer ID is missing.')
            );
        }

        $orderCollection = $this->orderCollectionFactory
            ->create()
            ->addFieldToFilter('customer_email', $customerEmail)
            ->addFieldToFilter(
                'state',
                ['nin' => ['canceled', 'closed', 'complete']]
            );

        return (bool) $orderCollection->getTotalCount();
    }
}
