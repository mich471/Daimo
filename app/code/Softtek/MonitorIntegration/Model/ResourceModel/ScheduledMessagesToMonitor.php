<?php
/**
 * Copyright © Softtek 2020 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Softtek\MonitorIntegration\Model\ResourceModel;

class ScheduledMessagesToMonitor extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('softtek_monitorintegration_scheduledmessagestomonitor', 'scheduledmessagestomonitor_id');
    }
}

