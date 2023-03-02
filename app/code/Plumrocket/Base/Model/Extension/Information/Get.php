<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Information;

use Magento\Config\Model\Config\Structure;
use Magento\Config\Model\Config\Structure\Element\Field;
use Magento\Framework\Config\DataInterface;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\View\Layout;
use Magento\Framework\View\Layout\GeneratorPool;
use Plumrocket\Base\Api\Data\ExtensionInformationInterface;
use Plumrocket\Base\Api\GetExtensionInformationInterface;
use Plumrocket\Base\Helper\Base;
use Plumrocket\Base\Model\Extension\GetModuleName;
use Plumrocket\Base\Model\Extension\Information\ContainerFactory;
use Plumrocket\Base\Model\Extensions\GetInformation;

/**
 * @since 2.3.0
 */
class Get implements GetExtensionInformationInterface
{
    /**
     * @var string[]
     */
    private $services = [
        'Base',
        'Token',
        'AmpEmailApi',
    ];

    /**
     * @var \Plumrocket\Base\Api\ModuleInformationInterface[]
     */
    private $extensions;

    /**
     * @var \Plumrocket\Base\Model\Extension\Information\ContainerFactory
     */
    private $informationContainerFactory;

    /**
     * @var \Plumrocket\Base\Helper\Base
     */
    private $baseHelper;

    /**
     * @var \Magento\Config\Model\Config\Structure
     */
    private $configStructure;

    /**
     * @var \Plumrocket\Base\Model\Extension\GetModuleName
     */
    private $getExtensionName;

    /**
     * @var \Magento\Framework\View\Layout\GeneratorPool
     */
    private $generatorPool;

    /**
     * @var \Magento\Framework\Config\DataInterface
     */
    private $extensionsConfig;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    private $moduleList;

    /**
     * @var \Plumrocket\Base\Model\Extensions\GetInformation
     */
    private $getInformation;

    /**
     * @param \Plumrocket\Base\Model\Extension\Information\ContainerFactory $informationContainerFactory
     * @param \Plumrocket\Base\Helper\Base                                  $baseHelper
     * @param \Magento\Config\Model\Config\Structure                        $configStructure
     * @param \Magento\Framework\View\Layout\GeneratorPool                  $generatorPool
     * @param \Plumrocket\Base\Model\Extension\GetModuleName                $getExtensionName
     * @param \Magento\Framework\Config\DataInterface                       $extensionsConfig
     * @param \Magento\Framework\Module\ModuleListInterface                 $moduleList
     * @param \Plumrocket\Base\Model\Extensions\GetInformation              $getInformation
     * @param array                                                         $extensions
     */
    public function __construct(
        ContainerFactory $informationContainerFactory,
        Base $baseHelper,
        Structure $configStructure,
        GeneratorPool $generatorPool,
        GetModuleName $getExtensionName,
        DataInterface $extensionsConfig,
        ModuleListInterface $moduleList,
        GetInformation $getInformation,
        array $extensions = []
    ) {
        $this->extensions = $extensions;
        $this->informationContainerFactory = $informationContainerFactory;
        $this->baseHelper = $baseHelper;
        $this->configStructure = $configStructure;
        $this->getExtensionName = $getExtensionName;
        $this->generatorPool = $generatorPool;
        $this->extensionsConfig = $extensionsConfig;
        $this->moduleList = $moduleList;
        $this->getInformation = $getInformation;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $moduleName): ExtensionInformationInterface
    {
        $moduleName = $this->getExtensionName->execute($moduleName);

        // TODO: remove after adding pr_extensions.xml to all modules
        if ($this->getInformation->execute($moduleName)) {
            return $this->getInformation->execute($moduleName);
        }

        if (! isset($this->extensions[$moduleName])) {
            /** @var \Plumrocket\Base\Model\Extension\Information\Container $infoContainer */
            $infoContainer = $this->informationContainerFactory->create();
            $infoContainer->setModuleName($moduleName);

            if ($extensionConfig = $this->extensionsConfig->get($moduleName)) {
                $infoContainer->setIsService($extensionConfig['is_service'])
                    ->setModuleName($extensionConfig['name'])
                    ->setTitle($extensionConfig['title'])
                    ->setConfigSection($extensionConfig['config_section'])
                    ->setIsEnabledFieldConfigPath($extensionConfig['is_enabled_path'])
                    ->setDocumentationLink($extensionConfig['documentation'] ?: $extensionConfig['wiki']);
            } else {
                if ($this->moduleList->has("Plumrocket_$moduleName")) {
                    /**
                     * @var \Plumrocket\Base\Helper\Base $helper
                     */
                    $helper = $this->baseHelper->getModuleHelper($moduleName);

                    if (method_exists($helper, 'getConfigSectionId')) {
                        $infoContainer->setConfigSection($helper->getConfigSectionId());
                    } else {
                        $infoContainer->setConfigSection('');
                    }

                    $infoContainer->setIsService(in_array($moduleName, $this->services, true));

                    list($wiki, $title) = $this->getDataFromVersionField($infoContainer->getConfigSection());

                    if ($wiki && $title) {
                        $infoContainer->setTitle($title);
                        $infoContainer->setDocumentationLink($wiki);
                    }
                }
            }

            $this->extensions[$moduleName] = $infoContainer;
        }

        return $this->extensions[$moduleName];
    }

    /**
     * TODO: remove after adding pr_extensions.xml to all modules
     *
     * @param string $configSectionId
     * @return array
     */
    private function getDataFromVersionField(string $configSectionId): array
    {
        $wikiLink = '';
        $title = '';

        if ($configSectionId) {
            $versionField = $this->configStructure->getElementByConfigPath("{$configSectionId}/general/version");
            if ($versionField instanceof Field && $versionField->getFrontendModel()) {
                /** @var \Plumrocket\Base\Block\Adminhtml\System\Config\Form\Version $versionBlock */
                $versionBlock = $this->createBlock($versionField->getFrontendModel());
                if ($versionBlock) {
                    try {
                        $blockReflection = new \ReflectionClass($versionBlock);
                        $wikiProperty = $blockReflection->getProperty('wikiLink');
                        $wikiProperty->setAccessible(true);
                        $wikiLink = $wikiProperty->getValue($versionBlock);
                        $titleProperty = $blockReflection->getProperty('moduleTitle');
                        $titleProperty->setAccessible(true);
                        $title = $titleProperty->getValue($versionBlock);
                    } catch (\ReflectionException $e) {
                        $wikiLink = '';
                        $title = '';
                    }
                }
            }
        }

        return [$wikiLink, $title];
    }

    private function createBlock(string $type)
    {
        $blockGenerator = $this->generatorPool->getGenerator(Layout\Generator\Block::TYPE);
        return $blockGenerator->createBlock($type, $type, []);
    }
}
