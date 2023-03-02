<?php
/**
 * Copyright © Softtek 2020 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Softtek\MonitorIntegration\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ScheduledMessagesToMonitorRepositoryInterface
{

    /**
     * Save ScheduledMessagesToMonitor
     * @param \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface $scheduledMessagesToMonitor
     * @return \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface $scheduledMessagesToMonitor
    );

    /**
     * Retrieve ScheduledMessagesToMonitor
     * @param string $scheduledmessagestomonitorId
     * @return \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($scheduledmessagestomonitorId);

    /**
     * Retrieve ScheduledMessagesToMonitor matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete ScheduledMessagesToMonitor
     * @param \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface $scheduledMessagesToMonitor
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface $scheduledMessagesToMonitor
    );

    /**
     * Delete ScheduledMessagesToMonitor by ID
     * @param string $scheduledmessagestomonitorId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($scheduledmessagestomonitorId);
}

