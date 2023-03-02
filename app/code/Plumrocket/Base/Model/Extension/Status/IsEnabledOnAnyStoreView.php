<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Status;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManager;
use Plumrocket\Base\Api\ConfigUtilsInterface;
use Plumrocket\Base\Api\GetExtensionInformationInterface;
use Plumrocket\Base\Helper\Base;
use Plumrocket\Base\Model\Extension\GetModuleName;

/**
 * Check if module is enabled in configurations on any store view
 *
 * @since 2.5.0
 */
class IsEnabledOnAnyStoreView
{
    /**
     * @var \Plumrocket\Base\Helper\Base
     */
    private $baseHelper;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    private $storeManager;

    /**
     * @var \Plumrocket\Base\Model\Extension\GetModuleName
     */
    private $getExtensionName;

    /**
     * @var \Plumrocket\Base\Api\GetExtensionInformationInterface
     */
    private $getExtensionInformation;

    /**
     * @var \Plumrocket\Base\Api\ConfigUtilsInterface
     */
    private $configUtils;

    /**
     * @param \Plumrocket\Base\Helper\Base                          $baseHelper
     * @param \Magento\Store\Model\StoreManager                     $storeManager
     * @param \Plumrocket\Base\Model\Extension\GetModuleName        $getExtensionName
     * @param \Plumrocket\Base\Api\GetExtensionInformationInterface $getExtensionInformation
     * @param \Plumrocket\Base\Api\ConfigUtilsInterface             $configUtils
     */
    public function __construct(
        Base $baseHelper,
        StoreManager $storeManager,
        GetModuleName $getExtensionName,
        GetExtensionInformationInterface $getExtensionInformation,
        ConfigUtilsInterface $configUtils
    ) {
        $this->baseHelper = $baseHelper;
        $this->storeManager = $storeManager;
        $this->getExtensionName = $getExtensionName;
        $this->getExtensionInformation = $getExtensionInformation;
        $this->configUtils = $configUtils;
    }

    /**
     * Check if module is enabled on any store view.
     *
     * @param string $moduleName
     * @return bool
     */
    public function execute(string $moduleName): bool
    {
        $moduleName = $this->getExtensionName->execute($moduleName);
        $configPath = $this->getExtensionInformation->execute($moduleName)->getIsEnabledFieldConfigPath();

        foreach ($this->storeManager->getStores() as $store) {
            if (! $store->getIsActive()) {
                continue;
            }

            if ($configPath && $this->configUtils->getStoreConfig($configPath, $store->getId())) {
                return true;
            }

            try {
                $config = $this->baseHelper->getConfigHelper($moduleName);
                if ($config->isModuleEnabled($store->getId())) {
                    return true;
                }
            } catch (NoSuchEntityException $e) {
                $helper = $this->getHelper($moduleName);
                if ($helper && method_exists($helper, 'moduleEnabled')) {
                    if ($helper->moduleEnabled($store->getId())) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Receive helper
     *
     * @param string $moduleName
     * @return \Magento\Framework\App\Helper\AbstractHelper|\Plumrocket\Base\Helper\Base|false
     */
    private function getHelper(string $moduleName)
    {
        try {
            return $this->baseHelper->getModuleHelper($moduleName);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }
}
