<?php
/**
 * Copyright © Softtek 2020 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Softtek\MonitorIntegration\Model\ResourceModel\ScheduledMessagesToMonitor;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'scheduledmessagestomonitor_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Softtek\MonitorIntegration\Model\ScheduledMessagesToMonitor::class,
            \Softtek\MonitorIntegration\Model\ResourceModel\ScheduledMessagesToMonitor::class
        );
    }
}

