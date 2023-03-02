<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\DataPrivacy\Cron;

use Exception;
use Magento\Customer\Model\Customer;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Plumrocket\DataPrivacy\Helper\Config;
use Plumrocket\DataPrivacy\Model\Account\Remover;
use Plumrocket\DataPrivacy\Model\OptionSource\RemovalStatus;
use Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest\CollectionFactory;
use Psr\Log\LoggerInterface;

/**
 * Scheduler to clean accounts marked to be deleted or anonymized.
 *
 * @since 3.1.0
 */
class RemoveCustomersByRequests
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry
     */
    private $indexerRegistry;

    /**
     * @var \Plumrocket\DataPrivacy\Model\Account\Remover
     */
    private $accountRemover;

    /**
     * @var \Plumrocket\DataPrivacy\Helper\Config
     */
    private $config;

    /**
     * @param \Psr\Log\LoggerInterface                                                     $logger
     * @param \Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry                                                  $registry
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                                  $dateTime
     * @param \Magento\Framework\Indexer\IndexerRegistry                                   $indexerRegistry
     * @param \Plumrocket\DataPrivacy\Model\Account\Remover                                $accountRemover
     * @param \Plumrocket\DataPrivacy\Helper\Config                                        $config
     */
    public function __construct(
        LoggerInterface $logger,
        CollectionFactory $collectionFactory,
        Registry $registry,
        DateTime $dateTime,
        IndexerRegistry $indexerRegistry,
        Remover $accountRemover,
        Config $config
    ) {
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
        $this->registry = $registry;
        $this->dateTime = $dateTime;
        $this->indexerRegistry = $indexerRegistry;
        $this->accountRemover = $accountRemover;
        $this->config = $config;
    }

    /**
     * Check for accounts which need to be deleted and delete them.
     *
     * @return void
     */
    public function execute()
    {
        if (! $this->config->isModuleEnabled() || !$this->config->isAccountDeletionEnabled()) {
            return;
        }

        /** @var \Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest\Collection $removalRequests */
        $removalRequests = $this->collectionFactory
            ->create()
            ->addFieldToFilter('scheduled_at', ['lteq' => date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp())])
            ->addFieldToFilter('status', ['eq' => RemovalStatus::PENDING]);

        if (! $removalRequests->getItems()) {
            return;
        }

        $isSecureArea = $this->registry->registry('isSecureArea');
        if (null !== $isSecureArea) {
            $this->registry->unregister('isSecureArea');
        }
        $this->registry->register('isSecureArea', true);

        $removedCustomerIds = [];
        foreach ($removalRequests->getItems() as $removalRequest) {
            try {
                $customerId = (int) $removalRequest->getCustomerId();

                $result = $this->accountRemover->execute($removalRequest);
                if ($result && $customerId) {
                    $removedCustomerIds[] = $customerId;
                }
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        if (! empty($removedCustomerIds)) {
            $indexer = $this->indexerRegistry->get(Customer::CUSTOMER_GRID_INDEXER_ID);
            $indexer->reindexList($removedCustomerIds);
        }

        $this->registry->unregister('isSecureArea');
        if (null !== $isSecureArea) {
            $this->registry->register('isSecureArea', $isSecureArea);
        }
    }
}
