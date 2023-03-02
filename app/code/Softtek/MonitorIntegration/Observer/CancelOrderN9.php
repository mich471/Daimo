<?php

namespace Softtek\MonitorIntegration\Observer;

use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Psr\Log\LoggerInterface;
use Softtek\MonitorIntegration\Helper\SchedulesMessagesHelper;
use Softtek\MonitorIntegration\Model\Data\ScheduledMessagesToMonitor;
use Softtek\MonitorIntegration\Model\Enum\MonitorInterfacesName;
use Softtek\MonitorIntegration\Model\Enum\ScheduledMessageStatus;
use Softtek\MonitorIntegration\Model\ScheduledMessagesToMonitorRepository;

class CancelOrderN9 implements \Magento\Framework\Event\ObserverInterface
{

    /**
    * @var LoggerInterface
    */
    private $logger;

    /**
    * @var \Magento\Framework\ObjectManagerInterface
    */
    protected $_objectManager;

    /**
    * @var ScheduledMessagesToMonitorRepository
    */
    protected $monitorRepository;

    /**
    * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
    */
    protected $_date;
    /**
     * @var SchedulesMessagesHelper
     */
    private $scheduledMessageHelper;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ScheduledMessagesToMonitorRepository $scheduledMessagesToMonitorRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        SchedulesMessagesHelper $helper,
        LoggerInterface $logger
    ) {
        $this->_objectManager = $objectManager;
        $this->monitorRepository = $scheduledMessagesToMonitorRepository;
        $this->_date = $date;
        $this->scheduledMessageHelper = $helper;
        $this->logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $this->scheduledMessageHelper->saveRemCancelacionMessage($order);
    }
}
