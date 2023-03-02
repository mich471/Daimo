<?php
/**
 * Copyright © Softtek 2020 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Softtek\MonitorIntegration\Model;

use Magento\Framework\Api\DataObjectHelper;
use Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface;
use Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterfaceFactory;

class ScheduledMessagesToMonitor extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $_eventPrefix = 'softtek_monitorintegration_scheduledmessagestomonitor';
    protected $scheduledmessagestomonitorDataFactory;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ScheduledMessagesToMonitorInterfaceFactory $scheduledmessagestomonitorDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Softtek\MonitorIntegration\Model\ResourceModel\ScheduledMessagesToMonitor $resource
     * @param \Softtek\MonitorIntegration\Model\ResourceModel\ScheduledMessagesToMonitor\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ScheduledMessagesToMonitorInterfaceFactory $scheduledmessagestomonitorDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Softtek\MonitorIntegration\Model\ResourceModel\ScheduledMessagesToMonitor $resource,
        \Softtek\MonitorIntegration\Model\ResourceModel\ScheduledMessagesToMonitor\Collection $resourceCollection,
        array $data = []
    ) {
        $this->scheduledmessagestomonitorDataFactory = $scheduledmessagestomonitorDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve scheduledmessagestomonitor model with scheduledmessagestomonitor data
     * @return ScheduledMessagesToMonitorInterface
     */
    public function getDataModel()
    {
        $scheduledmessagestomonitorData = $this->getData();
        
        $scheduledmessagestomonitorDataObject = $this->scheduledmessagestomonitorDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $scheduledmessagestomonitorDataObject,
            $scheduledmessagestomonitorData,
            ScheduledMessagesToMonitorInterface::class
        );
        
        return $scheduledmessagestomonitorDataObject;
    }
}

