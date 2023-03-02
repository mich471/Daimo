<?php
/**
 * Copyright © Softtek 2020 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Softtek\MonitorIntegration\Api\Data;

interface ScheduledMessagesToMonitorSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get ScheduledMessagesToMonitor list.
     * @return \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface[]
     */
    public function getItems();

    /**
     * Set order_id list.
     * @param \Softtek\MonitorIntegration\Api\Data\ScheduledMessagesToMonitorInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

