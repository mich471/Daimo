<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context as HelperContext;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Plumrocket\Base\Api\ExtensionStatusInterface;

/**
 * @since 1.0.0
 * @deprecated since 2.5.0 - we move logic from this class to others
 */
class Base extends AbstractHelper
{
    /**
     * @deprecated since 2.3.9
     * @see ExtensionStatusInterface::NOT_INSTALLED
     */
    const MODULE_STATUS_NOT_INSTALLED = ExtensionStatusInterface::NOT_INSTALLED;
    /**
     * @deprecated since 2.3.9
     * @see ExtensionStatusInterface::DISABLED
     */
    const MODULE_STATUS_DISABLED = ExtensionStatusInterface::DISABLED;
    /**
     * @deprecated since 2.3.9
     * @see ExtensionStatusInterface::ENABLED
     */
    const MODULE_STATUS_ENABLED = ExtensionStatusInterface::ENABLED;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Each module must override this value
     * @var string
     */
    protected $_configSectionId;

    /**
     * Initialize helper
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager,
     * @param HelperContext                             $context
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        HelperContext $context
    ) {
        $this->_objectManager = $objectManager;
        parent::__construct($context);
    }

    /**
     * @return string
     */
    protected function getMktpKey()
    {
        return '532416486b540ea2a1e50c4070b671611b44f52718';
    }

    /**
     * @deprecated since 2.1.9 - use getCurrentConfig instead
     *
     * @return mixed
     */
    protected function getCurentConfig()
    {
        return $this->getCurrentConfig();
    }

    /**
     * @since 2.1.9
     *
     * @return mixed
     */
    protected function getCurrentConfig()
    {
        return $this->getConfig($this->getModName() . '/module/data');
    }

    /**
     * @param string $customerKey
     * @return string
     */
    protected function getTrueCustomerKey($customerKey)
    {
        $trueKey = '';

        if ($this->isMarketplace($customerKey)) {
            $trueKey = $this->getCurrentConfig();
        }

        return $trueKey ?: $customerKey;
    }

    /**
     * @return string
     */
    private function getModName()
    {
        $data = explode('_', $this->_getModuleName());

        return $data[1] ?? '';
    }

    /**
     * @param $customerKey
     * @return bool
     */
    public function isMarketplace($customerKey)
    {
        return $customerKey === $this->getMktpKey();
    }

    /**
     * @return array
     */
    public function preparedData()
    {
        return ['magento_version' => $this->getMagento2Version()];
    }

    /**
     * Receive config section id
     *
     * @deprecated since 2.3.0 - use \Plumrocket\Base\Api\GetExtensionInformationInterface
     * @return string
     */
    public function getConfigSectionId()
    {
        return $this->_configSectionId;
    }

    /**
     * Receive magento config value
     * @deprecated since 2.3.0
     * @see \Plumrocket\Base\Helper\AbstractConfig::getConfig
     *
     * @param string      $path
     * @param string|int  $store
     * @param string|null $scope
     * @return mixed
     */
    public function getConfig($path, $store = null, $scope = null)
    {
        if ($scope === null) {
            $scope = ScopeInterface::SCOPE_STORE;
        }
        return $this->scopeConfig->getValue($path, $scope, $store);
    }

    /**
     * Receive backtrace
     *
     * @param  string  $title
     * @return string
     * @noinspection HtmlDeprecatedAttribute
     * @deprecated since 2.6.0
     * @see \Plumrocket\Base\Model\Utils\Debug::backtrace
     */
    public static function backtrace($title = 'Debug Backtrace:')
    {
        $output = '';
        $output .= '<hr /><div>' . $title . '<br /><table border="1" cellpadding="2" cellspacing="2">';

        $stacks = debug_backtrace();

        $output .= '<thead>' .
            '<tr>' .
            '<th><strong>File</strong></th>' .
            '<th><strong>Line</strong></th>' .
            '<th><strong>Function</strong></th>' .
            '</tr>' .
            '</thead>';

        foreach ($stacks as $stack) {
            if (! isset($stack['file'])) {
                $stack['file'] = '[PHP Kernel]';
            }
            if (! isset($stack['line'])) {
                $stack['line'] = '';
            }

            $output .= "<tr><td>{$stack['file']}</td><td>{$stack['line']}</td>".
                "<td>{$stack['function']}</td></tr>";
        }
        $output .= '</table></div><hr /></p>';
        return $output;
    }

    /**
     * @deprecated since 2.3.0
     * @see \Plumrocket\Base\Api\ExtensionStatusInterface::isEnabled
     *
     * @param  string $moduleName
     * @return bool
     */
    public function moduleExists($moduleName)
    {
        return $this->getModuleStatus($moduleName) ?: false;
    }

    /**
     * Receive status of Plumrocket module
     * @deprecated since 2.3.9
     * @see \Plumrocket\Base\Api\ExtensionStatusInterface
     *
     * @param string $moduleName e.g. SocialLoginFree
     * @return int
     * @api
     * @since 2.3.0
     */
    public function getModuleStatus(string $moduleName)
    {
        $hasModule = $this->_moduleManager->isEnabled("Plumrocket_$moduleName");
        if (! $hasModule) {
            return self::MODULE_STATUS_NOT_INSTALLED;
        }

        try {
            return $this->getConfigHelper($moduleName)->isModuleEnabled()
                ? self::MODULE_STATUS_ENABLED
                : self::MODULE_STATUS_DISABLED;
        } catch (NoSuchEntityException $e) {
            try {
                $dataHelper = $this->getModuleHelper($moduleName);
                if (method_exists($dataHelper, 'moduleEnabled')) {
                    return $dataHelper->moduleEnabled()
                        ? self::MODULE_STATUS_ENABLED
                        : self::MODULE_STATUS_DISABLED;
                }

                return self::MODULE_STATUS_ENABLED;
            } catch (NoSuchEntityException $e) {
                return self::MODULE_STATUS_ENABLED;
            }
        }
    }

    /**
     * Receive helper
     *
     * @param string $moduleName
     * @return \Magento\Framework\App\Helper\AbstractHelper
     */
    public function getModuleHelper($moduleName)
    {
        $type = "Plumrocket\\{$moduleName}\Helper\Data";
        try {
            $dataHelper = $this->_objectManager->get($type);
        } catch (\ReflectionException $reflectionException) {
            return $this->_objectManager->get(DataFallback::class);
        }

        return $dataHelper;
    }

    /**
     * @param string $moduleName e.g. SizeChart
     * @return \Plumrocket\Base\Helper\AbstractConfig
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfigHelper(string $moduleName)
    {
        $type = "Plumrocket\\{$moduleName}\Helper\Config";
        try {
            $configHelper = $this->_objectManager->get($type);
        } catch (\ReflectionException $reflectionException) {
            throw new NoSuchEntityException(__('Cannot create %1', $type));
        }

        if ($configHelper && $configHelper instanceof AbstractConfig) {
            return $configHelper;
        }

        throw new NoSuchEntityException(__('Cannot create %1', $type));
    }

    /**
     * Magento 2 version
     *
     * @return string
     */
    public function getMagento2Version()
    {
        /** @var \Magento\Framework\App\ProductMetadataInterface $productMetadata */
        $productMetadata = $this->_objectManager->get(ProductMetadataInterface::class);

        return $productMetadata->getVersion();
    }
}
