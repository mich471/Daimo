<?php


namespace Softtek\MonitorIntegration\Helper;

use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Psr\Log\LoggerInterface;

class SendEmailHelper extends AbstractHelper
{
    const GENERAL_FAILURE_EMAIL_TEMPLATE = 'monitor_email_general_failure';

    const BAD_COMUNICATION_EMAIL_TEMPLATE = 'monitor_email_template';

    private $notificationEmail;

    protected $configHelper;

    protected $storeManager;

    protected $logger;

    /**
     * SendEmailHelper constructor.
     * @param ConfigHelper $configHelper
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConfigHelper $configHelper,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        LoggerInterface $logger
    )
    {
        $this->configHelper = $configHelper;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->notificationEmail = $configHelper->getNotificationEmail();
        $this->logger = $logger;
    }


    public function generalFailureEmail($scheduledMessage, $exceptionMessage) {
        $this->logger->info("Generating email for general failure error " . json_encode($scheduledMessage));
        $orderId = $scheduledMessage->getOrderId();
        $templateVars = [
            'orderId' => $orderId,
            'exception' => $exceptionMessage,
            'dateLastRequest' => $scheduledMessage->getCreatedDate(),
            'monitorInterface' => $scheduledMessage->getMonitorInterface()
        ];
        $this->sendEmail(SELF::GENERAL_FAILURE_EMAIL_TEMPLATE, $templateVars);
    }

    public function badComunicationErrorEmail($scheduledMessage, $payload, $response) {
        $this->logger->info("Generating email for bad communication error " . json_encode($scheduledMessage));
        $orderId = $scheduledMessage->getOrderId();
        $templateVars = [
            'orderId' => $orderId,
            'monitorRequest' => json_encode($payload),
            'monitorResponse' => json_encode($response),
            'dateLastRequest' => $scheduledMessage->getCreatedDate(),
            'monitorInterface' => $scheduledMessage->getMonitorInterface()
        ];
        $this->sendEmail(SELF::BAD_COMUNICATION_EMAIL_TEMPLATE, $templateVars);
    }

    private function sendEmail($template, $templateVars) {
        $this->logger->info("Sending email error " . json_encode($template));
        $transport = $this->transportBuilder->setTemplateIdentifier($template)
            ->setTemplateOptions(
                [
                    'area' => Area::AREA_FRONTEND,
                    'store' => $this->storeManager->getStore()->getId()
                ]
            )
            ->setTemplateVars($templateVars)
            ->setFrom('general')
            ->addTo($this->notificationEmail)
            ->getTransport();

        try {
            $transport->sendMessage();
        } catch (\exception $e) {
            $this->logger->error("Error while sending the notification email " . $e->getMessage());
        }
        return $this;
    }
}
