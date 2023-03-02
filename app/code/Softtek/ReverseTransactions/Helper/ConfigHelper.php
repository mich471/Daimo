<?php


namespace Softtek\ReverseTransactions\Helper;


use Magento\Framework\App\Helper\Context;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;

/**
 * Class ConfigHelper
 * @package Softtek\MonitorIntegration\Helper
 */
class ConfigHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     *
     */
    const NOTIFY_REVERSE_TRANSACTIONS_MAIL = "sportico/general/notify_reverse_transactions_mail";

    /**
     *
     */
    const REPORT_CONTROLLER_URL = "reversetransactions/index/getreport";

    /**
     *
     */
    const KEYS_CLIENT_FILE =  [
        "id",
        "customer_email",
        "increment_id",
        "transaction_id",
        "amount",
        "status",
        "payment_method",
        "is_processed",
        "processed_date",
        "has_error",
        "reverse_error_details"
    ];

    /**
     * @var Json
     */
    protected $serialize;

    /**
     * @var Resolver
     */
    private $localeResolver;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var UrlInterface
     */
    private $urlInterface;

    /**
     * DeliveryConfigurationHelper constructor.
     * @param Context $context
     * @param Json $serialize
     * @param Resolver $localeResolver
     * @param StoreManagerInterface $storeManager
     * @param UrlInterface $urlInterface
     */
    public function __construct(
        Context $context,
        Json $serialize,
        Resolver $localeResolver,
        StoreManagerInterface  $storeManager,
        UrlInterface $urlInterface
    )
    {
        $this->serialize = $serialize;
        $this->localeResolver = $localeResolver;
        $this->storeManager = $storeManager;
        $this->urlInterface = $urlInterface;
        parent::__construct($context);
    }


    /**
     * @return string|null
     */
    public function getNotificationEmail() {

        return $this->_getConfig(SELF::NOTIFY_REVERSE_TRANSACTIONS_MAIL);
    }

    /**
     * Retrieve Store Configuration Data
     *
     * @param   string $path
     * @return  string|null
     */
    public function _getConfig($path)
    {
        return $this->scopeConfig->getValue($path);
    }
}
