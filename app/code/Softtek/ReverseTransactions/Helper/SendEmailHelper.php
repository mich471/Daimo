<?php


namespace Softtek\ReverseTransactions\Helper;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Backend\Model\Url;
use Softtek\ReverseTransactions\Model\LoggerCsv;
use Psr\Log\LoggerInterface;

class SendEmailHelper extends AbstractHelper
{
    const REVERSE_TRANSACTIONS_EMAIL = 'reverse_transactions_template';

    private $notificationEmail;

    protected $configHelper;

    protected $storeManager;

    protected $logger;

    protected $scopeConfig;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;
    /**
     * @var LoggerCsv
     */
    private $loggerCsv;
    /**
     * @var Url
     */
    private $backendUrlManager;

    /**
     * SendEmailHelper constructor.
     * @param ConfigHelper $configHelper
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerCsv $loggerCsv
     * @param Url $backendUrlManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConfigHelper $configHelper,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        LoggerCsv $loggerCsv,
        Url $backendUrlManager,
        LoggerInterface $logger
    )
    {
        $this->configHelper = $configHelper;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->notificationEmail = $configHelper->getNotificationEmail();
        $this->scopeConfig = $scopeConfig;
        $this->loggerCsv = $loggerCsv;
        $this->backendUrlManager = $backendUrlManager;
        $this->logger = $logger;
    }

    /**
     * @param $transactionsLog
     * @param $type
     */
    public function sendReverseNotification($transactionsLog, $type, $title)
    {

        $filePath = $this->loggerCsv->writeEmailCsv($transactionsLog, $type);
        $template = self::REVERSE_TRANSACTIONS_EMAIL;
        $shortType = str_replace('softtek_payment', 'cybersource', $type);
        $filePathElements = explode('/', $filePath);
        $fileName = end($filePathElements);
        $url = $this->backendUrlManager->getBaseUrl() . ConfigHelper::REPORT_CONTROLLER_URL;

        $templateVars = [
            'title' => $title,
            'message' => 'Tipo de movimientos procesados: ' . $shortType,
            'fileUrl' => str_replace('softtek_', '', $url . "?rvtx=" . $fileName),
            'email_subject' => $title . '.'
        ];

        $this->sendEmail($template, $templateVars);
    }

    /**
     * @param $template
     * @param $templateVars
     * @param string $path
     * @return $this
     */
    private function sendEmail($template, $templateVars)
    {
        try {
            $store = $this->storeManager->getStore(1);
            $transport = $this->transportBuilder->setTemplateIdentifier($template)
                ->setTemplateOptions(
                    [
                        'area' => Area::AREA_FRONTEND,
                        'store' => $store->getId()
                    ]
                )
                ->setTemplateVars($templateVars)
                ->setFrom('general')
                ->addTo($this->notificationEmail)
                //->attachFile($path,'asd') //Add attachFile here
                ->getTransport();


            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->logger->error("Error while sending reverse notifications email " . $e->getMessage());
        }
        return $this;
    }
}
