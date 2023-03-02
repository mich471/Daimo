<?php
/**
 * Copyright © Softtek 2020 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Softtek\MonitorIntegration\Api\Data;

interface ScheduledMessagesToMonitorInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const NUMBER_OF_RETRIES = 'number_of_retries';
    const STATUS = 'status';
    const LAST_RETRY = 'last_retry';
    const LAST_RESPONSE = 'last_response';
    const LAST_REQUEST = 'last_request';
    const CREATED_DATE = 'created_date';
    const ORDER_ID = 'order_id';
    const SCHEDULEDMESSAGESTOMONITOR_ID = 'scheduledmessagestomonitor_id';
    const MONITOR_INTERFACE = 'monitor_interface';
    const ORDER_INCREMENTAL_ID = 'order_incremental_id';

    /**
     * Get scheduledmessagestomonitor_id
     * @return string|null
     */
    public function getScheduledmessagestomonitorId();

    /**
     * Set scheduledmessagestomonitor_id
     * @param string $scheduledmessagestomonitorId
     * @return \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface
     */
    public function setScheduledmessagestomonitorId($scheduledmessagestomonitorId);

    /**
     * Get order_id
     * @return string|null
     */
    public function getOrderId();

    /**
     * Set order_id
     * @param string $orderId
     * @return \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface
     */
    public function setOrderId($orderId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorExtensionInterface $extensionAttributes
    );

    /**
     * Get created_date
     * @return string|null
     */
    public function getCreatedDate();

    /**
     * Set created_date
     * @param string $createdDate
     * @return \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface
     */
    public function setCreatedDate($createdDate);

    /**
     * Get number_of_retries
     * @return string|null
     */
    public function getNumberOfRetries();

    /**
     * Set number_of_retries
     * @param string $numberOfRetries
     * @return \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface
     */
    public function setNumberOfRetries($numberOfRetries);

    /**
     * Get last_retry
     * @return string|null
     */
    public function getLastRetry();

    /**
     * Set last_retry
     * @param string $lastRetry
     * @return \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface
     */
    public function setLastRetry($lastRetry);

    /**
     * Get monitor_interface
     * @return string|null
     */
    public function getMonitorInterface();

    /**
     * Set monitor_interface
     * @param string $monitorInterface
     * @return \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface
     */
    public function setMonitorInterface($monitorInterface);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface
     */
    public function setStatus($status);

    /**
     * Get last_request
     * @return string|null
     */
    public function getLastRequest();

    /**
     * Set last_request
     * @param string $lastRequest
     * @return \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface
     */
    public function setLastRequest($lastRequest);

    /**
     * Get last_response
     * @return string|null
     */
    public function getLastResponse();

    /**
     * Set last_response
     * @param string $lastResponse
     * @return \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface
     */
    public function setLastResponse($lastResponse);

    /**
     * @return mixed
     */
    public function getOrderIncrementalId();

    /**
     * @return mixed
     */
    public function setOrderIncrementalId($orderIncrementalId);

}

