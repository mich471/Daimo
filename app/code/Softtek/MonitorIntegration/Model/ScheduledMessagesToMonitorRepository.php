<?php
/**
 * Copyright © Softtek 2020 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Softtek\MonitorIntegration\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterfaceFactory;
use Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorSearchResultsInterfaceFactory;
use Softtek\MonitorIntegration\Api\ScheduledMessagesToMonitorRepositoryInterface;
use Softtek\MonitorIntegration\Model\ResourceModel\ScheduledMessagesToMonitor as ResourceScheduledMessagesToMonitor;
use Softtek\MonitorIntegration\Model\ResourceModel\ScheduledMessagesToMonitor\CollectionFactory as ScheduledMessagesToMonitorCollectionFactory;

class ScheduledMessagesToMonitorRepository implements ScheduledMessagesToMonitorRepositoryInterface
{

    private $collectionProcessor;

    protected $resource;

    protected $extensibleDataObjectConverter;
    protected $searchResultsFactory;

    protected $dataObjectProcessor;

    private $storeManager;

    protected $dataScheduledMessagesToMonitorFactory;

    protected $scheduledMessagesToMonitorCollectionFactory;

    protected $extensionAttributesJoinProcessor;

    protected $scheduledMessagesToMonitorFactory;

    protected $dataObjectHelper;


    /**
     * @param ResourceScheduledMessagesToMonitor $resource
     * @param ScheduledMessagesToMonitorFactory $scheduledMessagesToMonitorFactory
     * @param ScheduledMessagesToMonitorInterfaceFactory $dataScheduledMessagesToMonitorFactory
     * @param ScheduledMessagesToMonitorCollectionFactory $scheduledMessagesToMonitorCollectionFactory
     * @param ScheduledMessagesToMonitorSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceScheduledMessagesToMonitor $resource,
        ScheduledMessagesToMonitorFactory $scheduledMessagesToMonitorFactory,
        ScheduledMessagesToMonitorInterfaceFactory $dataScheduledMessagesToMonitorFactory,
        ScheduledMessagesToMonitorCollectionFactory $scheduledMessagesToMonitorCollectionFactory,
        ScheduledMessagesToMonitorSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->scheduledMessagesToMonitorFactory = $scheduledMessagesToMonitorFactory;
        $this->scheduledMessagesToMonitorCollectionFactory = $scheduledMessagesToMonitorCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataScheduledMessagesToMonitorFactory = $dataScheduledMessagesToMonitorFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface $scheduledMessagesToMonitor
    ) {
        /* if (empty($scheduledMessagesToMonitor->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $scheduledMessagesToMonitor->setStoreId($storeId);
        } */
        
        $scheduledMessagesToMonitorData = $this->extensibleDataObjectConverter->toNestedArray(
            $scheduledMessagesToMonitor,
            [],
            \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface::class
        );
        
        $scheduledMessagesToMonitorModel = $this->scheduledMessagesToMonitorFactory->create()->setData($scheduledMessagesToMonitorData);
        
        try {
            $this->resource->save($scheduledMessagesToMonitorModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the scheduledMessagesToMonitor: %1',
                $exception->getMessage()
            ));
        }
        return $scheduledMessagesToMonitorModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($scheduledMessagesToMonitorId)
    {
        $scheduledMessagesToMonitor = $this->scheduledMessagesToMonitorFactory->create();
        $this->resource->load($scheduledMessagesToMonitor, $scheduledMessagesToMonitorId);
        if (!$scheduledMessagesToMonitor->getId()) {
            throw new NoSuchEntityException(__('ScheduledMessagesToMonitor with id "%1" does not exist.', $scheduledMessagesToMonitorId));
        }
        return $scheduledMessagesToMonitor->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->scheduledMessagesToMonitorCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface::class
        );
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface $scheduledMessagesToMonitor
    ) {
        try {
            $scheduledMessagesToMonitorModel = $this->scheduledMessagesToMonitorFactory->create();
            $this->resource->load($scheduledMessagesToMonitorModel, $scheduledMessagesToMonitor->getScheduledmessagestomonitorId());
            $this->resource->delete($scheduledMessagesToMonitorModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the ScheduledMessagesToMonitor: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($scheduledMessagesToMonitorId)
    {
        return $this->delete($this->get($scheduledMessagesToMonitorId));
    }
}

