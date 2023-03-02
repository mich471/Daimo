<?php


namespace Softtek\MonitorIntegration\Helper;


use Magento\Framework\App\Helper\Context;
use Magento\Framework\Locale\Resolver;
use Magento\Store\Model\ScopeInterface;

/**
 * Class ConfigHelper
 * @package Softtek\MonitorIntegration\Helper
 */
class ConfigHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     *
     */
    const SERVICE_URL = "monitor_config/general/monitor_url";

    /**
     *
     */
    const NOTIFY_ERRORS_MAIL = "monitor_config/general/notify_errors_mail";

    /**
     *
     */
    const CLIENT_CODE = "monitor_config/general/client_code";

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serialize;

    /**
     * @var Resolver
     */
    private $localeResolver;

    /**
     * DeliveryConfigurationHelper constructor.
     * @param Context $context
     * @param \Magento\Framework\Serialize\Serializer\Json $serialize
     * @param Resolver $localeResolver
     */
    public function __construct(Context $context,
                                \Magento\Framework\Serialize\Serializer\Json $serialize,
                                Resolver $localeResolver)
    {
        $this->serialize = $serialize;
        $this->localeResolver = $localeResolver;
        parent::__construct($context);
    }


    /**
     * @return string|null
     */
    public function getServiceUrl() {
        return $this->_getConfig(SELF::SERVICE_URL);
    }

    /**
     * @return string|null
     */
    public function getNotificationEmail() {
        return $this->_getConfig(SELF::NOTIFY_ERRORS_MAIL);
    }

    /**
     * @return string|null
     */
    public function getClientCode() {
        return $this->_getConfig(SELF::CLIENT_CODE);
    }

    /**
     * Retrieve Store Configuration Data
     *
     * @param   string $path
     * @return  string|null
     */
    protected function _getConfig($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_WEBSITE);
    }
}
