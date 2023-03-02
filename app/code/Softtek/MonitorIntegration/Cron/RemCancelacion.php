<?php
/**
 * Copyright © Softtek 2020 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Softtek\MonitorIntegration\Cron;

use Softtek\MonitorIntegration\Helper\SchedulesMessagesHelper;
use Softtek\MonitorIntegration\Helper\SendEmailHelper;
use Softtek\MonitorIntegration\Model\Enum\MonitorInterfacesName;
use Softtek\MonitorIntegration\Model\Enum\ScheduledMessageStatus;
use Softtek\MonitorIntegration\Model\ScheduledMessagesToMonitorRepository;
use Softtek\MonitorIntegration\Service\MonitorIntegrationService;

class RemCancelacion
{

    /**
     * @var Magento\Sales\Model\OrderRepository
     */
    private $orderRepository;

    protected $_objectManager;

    protected $logger;

    private $service;

    protected $monitorRepository;

    /**
     * @var SendEmailHelper
     */
    protected $mailHelper;

    protected $date;
    /**
     * @var SchedulesMessagesHelper
     */
    private $scheduledMessageHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param MonitorIntegrationService $service
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
     * @param ScheduledMessagesToMonitorRepository $scheduledMessagesToMonitorRepository
     * @param SendEmailHelper $mailHelper
     * @param SchedulesMessagesHelper $schedulesMessagesHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        MonitorIntegrationService $service,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        ScheduledMessagesToMonitorRepository $scheduledMessagesToMonitorRepository,
        SendEmailHelper $mailHelper,
        SchedulesMessagesHelper $schedulesMessagesHelper,
        \Psr\Log\LoggerInterface $logger)
    {
        $this->_objectManager = $objectManager;
        $this->orderRepository = $orderRepository;
        $this->service = $service;
        $this->date = $date;
        $this->monitorRepository = $scheduledMessagesToMonitorRepository;
        $this->mailHelper = $mailHelper;
        $this->scheduledMessageHelper = $schedulesMessagesHelper;
        $this->logger = $logger;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $user_Id = (string)1;
        $accion = "Rechazo";
        $descripcion_incidencia = "Incidente";
        $codigoRechazo = 2;
        $payload = [];

        $this->logger->info("Cronjob RemCancelacion is starting.");

        $searchCriteriaBuilder = $this->_objectManager->create('Magento\Framework\Api\SearchCriteriaBuilder');
        $scheduledMessagesRepository = $this->_objectManager->get(ScheduledMessagesToMonitorRepository::class);

        $ordersSearch = $searchCriteriaBuilder
            ->addFilter('monitor_interface', MonitorInterfacesName::N9, 'eq')
            ->addFilter('status', ScheduledMessageStatus::PENDING, 'eq')
            ->create();

        $orders = $scheduledMessagesRepository->getList($ordersSearch)->getItems();
        $previousOrderId = -1;

        foreach ($orders as $orderN){
            $orderId = $orderN->getOrderId();
            if ($orderId != $previousOrderId) {
                $this->logger->info("Scheduled Message Data " . json_encode($orderId));
                $orderObj = $this->getOrderById($orderId);
                if($orderObj) {
                    $orderIncrementId = $orderN->getOrderIncrementalId();
                    $this->logger->info("orderIncrementId " . json_encode($orderIncrementId));
                    foreach ($orderObj->getAllItems() as $product) {

                        $productSku = $product->getSku();
                        $this->logger->info("ProductSku " . json_encode($productSku));
                        $payload = [
                            "order_number" => $orderIncrementId,
                            "codigoSku" => $productSku,
                            "user_id" => $user_Id,
                            "accion" => $accion,
                            "descripcion_incidencia" => $descripcion_incidencia,
                            "codigoRechazo" => $codigoRechazo
                        ];
                        $response = $this->service->executeRemCancelacion($payload);
                        $this->logger->info("Response " . json_encode($response));
                        $numberOfRetries = $orderN->getNumberOfRetries();
                        $status = ScheduledMessageStatus::PROCESED;

                        $orderN->setLastRetry($this->date->date()->getTimestamp());
                        $orderN->setLastRequest(json_encode($payload));
                        $responseFromWs = isset($response->error) ? $response->error : $response->getBody();;

                        if ((isset($response->error)) || ((int)$response->getStatusCode() != 200)) {
                            $orderN->setNumberOfRetries(++$numberOfRetries);
                            $status = ScheduledMessageStatus::PENDING;
                            if ($numberOfRetries >= 2) {
                                $status = ScheduledMessageStatus::ERROR;
                            }
                        }

                        $orderN->setLastResponse($responseFromWs);
                        $orderN->setStatus($status);
                        $this->monitorRepository->save($orderN);

                        if ($numberOfRetries >= 2) {
                            $this->mailHelper->badComunicationErrorEmail($orderN, $payload, $responseFromWs);
                        } else {
                            $this->scheduledMessageHelper->confirmByMonitor($orderId, "SKU {$productSku} en orden {$orderIncrementId} cancelado por monitor.", false);
                        }
                    }
                }
                $previousOrderId = $orderId;
            }
        }
    }

    protected function getOrderById($id) {
        return $this->scheduledMessageHelper->getInfoFromOrder($id);
    }
}

