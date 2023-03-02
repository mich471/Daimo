<?php


namespace Softtek\MonitorIntegration\Helper;


use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Softtek\MonitorIntegration\Model\Enum\MonitorInterfacesName;
use Softtek\MonitorIntegration\Model\Enum\ScheduledMessageStatus;
use Softtek\MonitorIntegration\Model\ScheduledMessagesToMonitorRepository;

/**
 * Class SchedulesMessagesHelper
 * @package Softtek\MonitorIntegration\Helper
 */
class SchedulesMessagesHelper
{

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var ScheduledMessagesToMonitorRepository
     */
    protected $monitorRepository;

    /**
     * @var TimezoneInterface
     */
    protected $_date;

    /**
     * @var OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * OnepageControllerSuccessAction constructor.
     * @param ObjectManagerInterface $objectManager
     * @param ScheduledMessagesToMonitorRepository $scheduledMessagesToMonitorRepository
     * @param TimezoneInterface $date
     * @param OrderRepositoryInterface $orderRepository
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ScheduledMessagesToMonitorRepository $scheduledMessagesToMonitorRepository,
        TimezoneInterface $date,
        OrderRepositoryInterface $orderRepository,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_objectManager = $objectManager;
        $this->monitorRepository = $scheduledMessagesToMonitorRepository;
        $this->_date = $date;
        $this->_orderRepository = $orderRepository;
        $this->logger = $logger;

    }

    /**
     * @param $order
     */
    public function saveMessage($order) {
        $this->save($order, MonitorInterfacesName::N1);
    }

    /**
     * @param $order
     */
    public function saveRemCancelacionMessage($order) {
        $this->save($order, MonitorInterfacesName::N9);
    }

    /**
     * @param $order
     * @param $monitorInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function save($order, $monitorInterface)
    {
        $searchCriteriaBuilder = $this->_objectManager->create('Magento\Framework\Api\SearchCriteriaBuilder');
        $scheduledMessagesRepository = $this->_objectManager->get(ScheduledMessagesToMonitorRepository::class);
        $orderId = $order->getEntityId();
        $ordersSearch = $searchCriteriaBuilder
            ->addFilter('monitor_interface', $monitorInterface, 'eq')
            ->addFilter('order_id', $orderId, 'eq')
            ->addFilter('order_incremental_id', $order->getIncrementId(), 'eq')
            ->create();

        $scheduledMessages = $scheduledMessagesRepository->getList($ordersSearch)->getItems();
        $this->logger->info("Scheduled MEssages " . json_encode($scheduledMessages));
        if (empty($scheduledMessages)) {
            $scheduledMessage = $this->_objectManager->create('Softtek\MonitorIntegration\Model\Data\ScheduledMessagesToMonitor');

            $scheduledMessage->setOrderId($order->getEntityId());
            $scheduledMessage->setCreatedDate($this->_date->date()->getTimestamp());
            $scheduledMessage->setNumberOfRetries(0);
            $scheduledMessage->setMonitorInterface($monitorInterface);
            $scheduledMessage->setStatus(ScheduledMessageStatus::PENDING);
            $scheduledMessage->setOrderIncrementalId($order->getIncrementId());

            $this->monitorRepository->save($scheduledMessage);
        }
    }

    /**
     * @param $orderId
     * @return OrderInterface
     */
    public function getInfoFromOrder($orderId)
    {
        return $this->_orderRepository->get($orderId);
    }


    /**
     * @param $order
     * @return OrderInterface
     */
    public function updateOrder($order, $incrementId) {
        $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING, true)->save();
        $order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING, true)->save();
        $order->addStatusToHistory($order->getStatus(), 'Orden actualizada con id ' . $incrementId);

        return $this->_orderRepository->save($order);
    }

    public function confirmByMonitor($orderId, $message, $processing) {
        $order = $this->getInfoFromOrder($orderId);
        if ($processing && $order->getStatus() != 'pending') {
            $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING, true)->save();
            $order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING, true)->save();
        }
        $order->addCommentToStatusHistory($message, $order->getStatus(), false);
        $this->_orderRepository->save($order);
    }
}
