<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Status;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\Module\ModuleListInterface;
use Plumrocket\Base\Api\ConfigUtilsInterface;
use Plumrocket\Base\Api\ExtensionStatusInterface;
use Plumrocket\Base\Api\GetExtensionInformationInterface;
use Plumrocket\Base\Api\GetExtensionStatusInterface;
use Plumrocket\Base\Helper\Base;
use Plumrocket\Base\Model\Extension\GetModuleName;

/**
 * Class GetModuleVersion
 *
 * @since 2.3.9
 */
class Get implements GetExtensionStatusInterface
{
    /**
     * @var \Plumrocket\Base\Helper\Base
     */
    private $baseHelper;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    private $fullModuleList;

    /**
     * @var \Plumrocket\Base\Api\GetExtensionInformationInterface
     */
    private $getExtensionInformation;

    /**
     * @var \Plumrocket\Base\Api\ConfigUtilsInterface
     */
    private $configUtils;

    /**
     * @var \Plumrocket\Base\Model\Extension\GetModuleName
     */
    private $getModuleName;

    /**
     * @param \Plumrocket\Base\Helper\Base                          $baseHelper
     * @param \Magento\Framework\Module\Manager                     $moduleManager
     * @param \Magento\Framework\Module\ModuleListInterface         $fullModuleList
     * @param \Plumrocket\Base\Api\GetExtensionInformationInterface $getExtensionInformation
     * @param \Plumrocket\Base\Api\ConfigUtilsInterface             $configUtils
     * @param \Plumrocket\Base\Model\Extension\GetModuleName        $getModuleName
     */
    public function __construct(
        Base $baseHelper,
        ModuleManager $moduleManager,
        ModuleListInterface $fullModuleList,
        GetExtensionInformationInterface $getExtensionInformation,
        ConfigUtilsInterface $configUtils,
        GetModuleName $getModuleName
    ) {
        $this->baseHelper = $baseHelper;
        $this->moduleManager = $moduleManager;
        $this->fullModuleList = $fullModuleList;
        $this->getExtensionInformation = $getExtensionInformation;
        $this->configUtils = $configUtils;
        $this->getModuleName = $getModuleName;
    }

    /**
     * Retrieve status of Plumrocket module
     *
     * @param string $moduleName
     * @return int
     */
    public function execute(string $moduleName): int
    {
        $moduleName = $this->getModuleName->execute($moduleName);

        $hasModule = $this->moduleManager->isEnabled("Plumrocket_$moduleName");
        if (! $hasModule) {
            return $this->fullModuleList->has("Plumrocket_$moduleName")
                ? ExtensionStatusInterface::DISABLED_FROM_CLI
                : ExtensionStatusInterface::NOT_INSTALLED;
        }

        $extensionInformation = $this->getExtensionInformation->execute($moduleName);
        if ($isEnabledConfigPath = $extensionInformation->getIsEnabledFieldConfigPath()) {
            return $this->configUtils->isSetFlag($isEnabledConfigPath)
                ? ExtensionStatusInterface::ENABLED
                : ExtensionStatusInterface::DISABLED;
        }

        // TODO: remove old logic
        try {
            return $this->baseHelper->getConfigHelper($moduleName)->isModuleEnabled()
                ? ExtensionStatusInterface::ENABLED
                : ExtensionStatusInterface::DISABLED;
        } catch (NoSuchEntityException $e) {
            try {
                $dataHelper = $this->baseHelper->getModuleHelper($moduleName);
                if (method_exists($dataHelper, 'moduleEnabled')) {
                    return $dataHelper->moduleEnabled()
                        ? ExtensionStatusInterface::ENABLED
                        : ExtensionStatusInterface::DISABLED;
                }
            } catch (NoSuchEntityException $e) {
                return ExtensionStatusInterface::ENABLED;
            }
        }

        return ExtensionStatusInterface::ENABLED;
    }
}
