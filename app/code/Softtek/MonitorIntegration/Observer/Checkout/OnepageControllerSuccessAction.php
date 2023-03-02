<?php
/**
 * Copyright © Softtek 2020 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Softtek\MonitorIntegration\Observer\Checkout;

use Softtek\MonitorIntegration\Helper\SchedulesMessagesHelper;
use Softtek\MonitorIntegration\Model\ScheduledMessagesToMonitorRepository;

class OnepageControllerSuccessAction implements \Magento\Framework\Event\ObserverInterface
{
    private $schedulesMessagesHelper;

    /**
     * OnepageControllerSuccessAction constructor.
     * @param SchedulesMessagesHelper $schedulesMessagesHelper
     */
    public function __construct(
        SchedulesMessagesHelper $schedulesMessagesHelper
    )
    {
        $this->schedulesMessagesHelper = $schedulesMessagesHelper;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $order = $observer->getEvent()->getOrder();
        $this->schedulesMessagesHelper->saveMessage($order);
    }
}

