<?php
/**
 * Copyright © Softtek 2020 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Softtek\MonitorIntegration\Model\Data;

use Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface;

class ScheduledMessagesToMonitor extends \Magento\Framework\Api\AbstractExtensibleObject implements ScheduledMessagesToMonitorInterface
{

    /**
     * Get scheduledmessagestomonitor_id
     * @return string|null
     */
    public function getScheduledmessagestomonitorId()
    {
        return $this->_get(self::SCHEDULEDMESSAGESTOMONITOR_ID);
    }

    /**
     * Set scheduledmessagestomonitor_id
     * @param string $scheduledmessagestomonitorId
     * @return \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface
     */
    public function setScheduledmessagestomonitorId($scheduledmessagestomonitorId)
    {
        return $this->setData(self::SCHEDULEDMESSAGESTOMONITOR_ID, $scheduledmessagestomonitorId);
    }

    /**
     * Get order_id
     * @return string|null
     */
    public function getOrderId()
    {
        return $this->_get(self::ORDER_ID);
    }

    /**
     * Set order_id
     * @param string $orderId
     * @return \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get created_date
     * @return string|null
     */
    public function getCreatedDate()
    {
        return $this->_get(self::CREATED_DATE);
    }

    /**
     * Set created_date
     * @param string $createdDate
     * @return \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface
     */
    public function setCreatedDate($createdDate)
    {
        return $this->setData(self::CREATED_DATE, $createdDate);
    }

    /**
     * Get number_of_retries
     * @return string|null
     */
    public function getNumberOfRetries()
    {
        return $this->_get(self::NUMBER_OF_RETRIES);
    }

    /**
     * Set number_of_retries
     * @param string $numberOfRetries
     * @return \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface
     */
    public function setNumberOfRetries($numberOfRetries)
    {
        return $this->setData(self::NUMBER_OF_RETRIES, $numberOfRetries);
    }

    /**
     * Get last_retry
     * @return string|null
     */
    public function getLastRetry()
    {
        return $this->_get(self::LAST_RETRY);
    }

    /**
     * Set last_retry
     * @param string $lastRetry
     * @return \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface
     */
    public function setLastRetry($lastRetry)
    {
        return $this->setData(self::LAST_RETRY, $lastRetry);
    }

    /**
     * Get monitor_interface
     * @return string|null
     */
    public function getMonitorInterface()
    {
        return $this->_get(self::MONITOR_INTERFACE);
    }

    /**
     * Set monitor_interface
     * @param string $monitorInterface
     * @return \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface
     */
    public function setMonitorInterface($monitorInterface)
    {
        return $this->setData(self::MONITOR_INTERFACE, $monitorInterface);
    }

    /**
     * Get status
     * @return string|null
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * Set status
     * @param string $status
     * @return \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get last_request
     * @return string|null
     */
    public function getLastRequest()
    {
        return $this->_get(self::LAST_REQUEST);
    }

    /**
     * Set last_request
     * @param string $lastRequest
     * @return \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface
     */
    public function setLastRequest($lastRequest)
    {
        return $this->setData(self::LAST_REQUEST, $lastRequest);
    }

    /**
     * Get last_response
     * @return string|null
     */
    public function getLastResponse()
    {
        return $this->_get(self::LAST_RESPONSE);
    }

    /**
     * Set last_response
     * @param string $lastResponse
     * @return \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface
     */
    public function setLastResponse($lastResponse)
    {
        return $this->setData(self::LAST_RESPONSE, $lastResponse);
    }


    /**
     * @return mixed|null
     */
    public function getOrderIncrementalId()
    {
        return $this->_get(self::ORDER_INCREMENTAL_ID);
    }

    /**
     * @param $orderIncrementalId
     * @return mixed|ScheduledMessagesToMonitor
     */
    public function setOrderIncrementalId($orderIncrementalId)
    {
        return $this->setData(self::ORDER_INCREMENTAL_ID, $orderIncrementalId);
    }
}

