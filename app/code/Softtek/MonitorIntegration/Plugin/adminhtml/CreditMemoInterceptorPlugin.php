<?php

namespace Softtek\MonitorIntegration\Plugin\adminhtml;

use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order;
use Softtek\MonitorIntegration\Helper\SchedulesMessagesHelper;
use Magento\Framework\App\ResourceConnection;
use Softtek\MonitorIntegration\Model\Enum\MonitorInterfacesName;
use Softtek\MonitorIntegration\Model\Enum\ScheduledMessageStatus;
use Softtek\MonitorIntegration\Model\ScheduledMessagesToMonitorRepository;
use Magento\Framework\Api\SortOrderBuilder;

class CreditMemoInterceptorPlugin
{

    protected $logger;
    /**
     * @var OrderRepositoryInterface
     */
    private $_orderRepository;
    /**
     * @var SchedulesMessagesHelper
     */
    private $scheduledMessagesHelper;
    /**
     * @var Order
     */
    private $_orderResouceModel;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * CreditMemoInterceptorPlugin constructor.
     * @param ObjectManagerInterface $objectManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param OrderRepositoryInterface $orderRepository
     * @param Order $orderResourceModel
     * @param ResourceConnection $resourceConnection
     * @param SchedulesMessagesHelper $helper
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        \Psr\Log\LoggerInterface $logger,
        OrderRepositoryInterface $orderRepository,
        Order $orderResourceModel,
        ResourceConnection $resourceConnection,
        SortOrderBuilder $sortOrderBuilder,
        SchedulesMessagesHelper $helper
    )
    {
        $this->objectManager = $objectManager;
        $this->_orderRepository = $orderRepository;
        $this->logger = $logger;
        $this->_orderResouceModel = $orderResourceModel;
        $this->resourceConnection = $resourceConnection;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->scheduledMessagesHelper =$helper;
    }

    public function afterSave(
        \Magento\Sales\Api\CreditmemoRepositoryInterface $subject,
        $result
    ) {
        $orderId = $result->getOrderId();

        $order = $this->scheduledMessagesHelper->getInfoFromOrder($orderId);

        $incrementId = $this->getLastIncrementalId($orderId);
        if (is_null($incrementId)) {
            $incrementId = $order->getIncrementId();
        }
        $order->setIncrementId($incrementId);

        //We need to generate a new N9 to pass this data to monitor and the POS
        $this->scheduledMessagesHelper->saveRemCancelacionMessage($order);

        $attachment = '.1';
        if (strpos($incrementId, '.')) {
            $increment = explode(".", $incrementId);
            $attachment = "." . ((int)$increment[1]+1);
            $incrementId = $increment[0];
        }
        $incrementId .= $attachment;
        $order->setRealOrderId($incrementId);
        $order->setEditIncrement($attachment);
        $order->setIncrementId($incrementId);

        $this->scheduledMessagesHelper->saveMessage($order);
        //We should throw a N9 to inform monitor and the POS that the original order was canceled or there was some
        // changes over it and a new one will be send
        return $result;
    }

    private function getLastIncrementalId($orderId) {
        $searchCriteriaBuilder = $this->objectManager->create('Magento\Framework\Api\SearchCriteriaBuilder');
        $scheduledMessagesRepository = $this->objectManager->get(ScheduledMessagesToMonitorRepository::class);
        $sortOrder = $this->sortOrderBuilder->setField('scheduledmessagestomonitor_id')->setDirection('DESC')->create();

        $ordersSearch = $searchCriteriaBuilder
            ->addFilter('order_id',$orderId, 'eq')
            ->setSortOrders([$sortOrder])
            ->create();

        $scheduledMessages = $scheduledMessagesRepository->getList($ordersSearch)->getItems();
        if (sizeof($scheduledMessages) > 0) {
            return $scheduledMessages[0]->getOrderIncrementalId();
        }
        return null;
    }
}
