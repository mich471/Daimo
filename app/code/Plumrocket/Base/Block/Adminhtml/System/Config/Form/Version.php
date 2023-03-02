<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Block\Adminhtml\System\Config\Form;

use Plumrocket\Base\Api\GetExtensionInformationInterface;
use Plumrocket\Base\Model\Extension\Updates\GetLastVersionMessage;
use Plumrocket\Base\Model\IsModuleInMarketplace;

/**
 * @since 1.0.0
 */
class Version extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Plumrocket\Base\Helper\Base
     */
    protected $baseHelper;

    /**
     * @deprecated
     */
    protected $_baseHelper;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;

    /**
     * @deprecated
     */
    protected $_moduleList;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @deprecated
     */
    protected $_moduleManager;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $storeManager;

    /**
     * @deprecated
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @deprecated
     */
    protected $_productMetadata;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $serverAddress;

    /**
     * @deprecated
     */
    protected $_serverAddress;

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $cacheManager;

    /**
     * @deprecated
     */
    protected $_cacheManager;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var string
     */
    protected $wikiLink;

    /**
     * @deprecated since 2.1.8
     * @var string
     */
    protected $_wikiLink;

    /**
     * @var string
     */
    protected $moduleTitle;

    /**
     * @deprecated since 2.1.8, use $moduleTitle instead
     *
     * @var string
     */
    protected $_moduleName;

    /**
     * @var \Plumrocket\Base\Api\GetModuleVersionInterface
     */
    protected $getModuleVersion;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $phpSerializer;

    /**
     * @var \Plumrocket\Base\Api\GetExtensionInformationInterface
     */
    private $getExtensionInformation;

    /**
     * @var \Magento\Framework\Data\Form\Element\AbstractElement
     */
    private $element;

    /**
     * @var \Plumrocket\Base\Model\IsModuleInMarketplace
     */
    private $isModuleInMarketplace;

    /**
     * @var \Plumrocket\Base\Model\Extension\Updates\GetLastVersionMessage
     */
    private $getLastVersionMessage;

    /**
     * Version constructor.
     *
     * @param \Plumrocket\Base\Helper\Base                                   $baseHelper
     * @param \Magento\Framework\Module\ModuleListInterface                  $moduleList
     * @param \Magento\Framework\Module\Manager                              $moduleManager
     * @param \Magento\Store\Model\StoreManager                              $storeManager
     * @param \Magento\Framework\App\ProductMetadataInterface                $productMetadata
     * @param \Magento\Framework\HTTP\PhpEnvironment\ServerAddress           $serverAddress
     * @param \Magento\Framework\App\CacheInterface                          $cacheManager
     * @param \Magento\Framework\ObjectManagerInterface                      $objectManager
     * @param \Magento\Backend\Block\Template\Context                        $context
     * @param \Plumrocket\Base\Api\GetModuleVersionInterface                 $getModuleVersion
     * @param \Magento\Framework\Serialize\SerializerInterface               $phpSerializer
     * @param \Plumrocket\Base\Api\GetExtensionInformationInterface          $getExtensionInformation
     * @param \Plumrocket\Base\Model\IsModuleInMarketplace                   $isModuleInMarketplace
     * @param \Plumrocket\Base\Model\Extension\Updates\GetLastVersionMessage $getLastVersionMessage
     * @param array                                                          $data
     */
    public function __construct(
        \Plumrocket\Base\Helper\Base $baseHelper,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\HTTP\PhpEnvironment\ServerAddress $serverAddress,
        \Magento\Framework\App\CacheInterface $cacheManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Block\Template\Context $context,
        \Plumrocket\Base\Api\GetModuleVersionInterface $getModuleVersion,
        \Magento\Framework\Serialize\SerializerInterface $phpSerializer,
        GetExtensionInformationInterface $getExtensionInformation,
        IsModuleInMarketplace $isModuleInMarketplace,
        GetLastVersionMessage $getLastVersionMessage,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->baseHelper = $this->_baseHelper = $baseHelper;
        $this->moduleList = $this->_moduleList = $moduleList;
        $this->moduleManager = $this->_moduleManager = $moduleManager;
        $this->storeManager = $this->_storeManager = $storeManager;
        $this->productMetadata = $this->_productMetadata  = $productMetadata;
        $this->serverAddress = $this->_serverAddress = $serverAddress;
        $this->cacheManager = $this->_cacheManager = $cacheManager;
        $this->_objectManager = $objectManager;
        $this->getModuleVersion = $getModuleVersion;
        $this->wikiLink = $this->wikiLink ?: $this->_wikiLink;
        $this->moduleTitle = $this->moduleTitle ?: $this->_moduleName;
        $this->phpSerializer = $phpSerializer;
        $this->getExtensionInformation = $getExtensionInformation;
        $this->isModuleInMarketplace = $isModuleInMarketplace;
        $this->getLastVersionMessage = $getLastVersionMessage;
    }

    /**
     * Render version field considering request parameter
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->element = $element;
        return $this->getModuleInfoHtml();
    }

    /**
     * Get Last Version
     * @deprecated since 2.5.0
     * @see \Plumrocket\Base\Model\Extension\Updates\GetLastVersionMessage::execute
     */
    protected function getLastVersionMessage()
    {
        return $this->getLastVersionMessage->execute($this->getModuleName());
    }

    /**
     * Receive url to extension documentation
     *
     * @return string
     */
    public function getWikiLink(): string
    {
        return $this->wikiLink ?: $this->getExtensionInformation->execute(
            $this->getModuleName()
        )->getDocumentationLink();
    }

    /**
     * Receive extension name
     *
     * @return string
     */
    public function getModuleTitle(): string
    {
        return $this->moduleTitle ?: $this->getExtensionInformation->execute($this->getModuleName())->getTitle();
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        if ($this->element && $this->element->getData('field_config/pr_extension_name')) {
            return $this->element->getData('field_config/pr_extension_name');
        }

        return parent::getModuleName();
    }

    /**
     * Receive extension information html
     *
     * @return string
     */
    public function getModuleInfoHtml()
    {
        $moduleVersion = $this->getModuleVersion->execute($this->getModuleName());

        if ($this->isModuleInMarketplace->execute($this->getModuleName())) {
            $message = $this->getModuleTitle() . ' v' . $moduleVersion . ' was developed by Plumrocket Inc. ' .
                'If you have any questions, please contact us at ' .
                '<a href="mailto:support@plumrocket.com">support@plumrocket.com</a>.';
        } else {
            $message = $this->getModuleTitle() . ' v' . $moduleVersion . ' was developed by ' .
                '<a href="https://plumrocket.com" target="_blank">Plumrocket Inc</a>.
            For manual & video tutorials please refer to ' .
                '<a href="' . $this->getWikiLink() . '" target="_blank">our online documentation</a>.';
        }

        $html = '<tr><td class="label" colspan="4" style="text-align: left;">' .
            '<div style="padding:10px;background-color:#f8f8f8;border:1px solid #ddd;margin-bottom:7px;">
            ' . $message . '</div></td></tr>';

        $mvd = strtolower($this->getModuleName()) . '_last_module_version';
        $tags = [];

        if ($mcache = $this->cacheManager->load($mvd)) {
            $mData = $this->phpSerializer->unserialize($mcache);
            $message = $mData['message'];
            $version = $mData['newv'];
        } else {
            $mcache = $this->getLastVersionMessage->execute($this->getModuleName());
            $message = $mcache['message'];
            $version = $mcache['newv'];
            if (!empty($message) && !empty($version)) {
                $this->cacheManager->save($this->phpSerializer->serialize($mcache), $mvd, $tags, 86400);
            }
        }

        $messageHtml = '';

        if (!empty($message) && !empty($version)) {
            if (version_compare($version, $moduleVersion, '>')) {
                $messageHtml = "<script type='text/javascript'>
                     require(['jquery'], function ($) {
                         var messageBlock = $('.page-main-actions'),
                         messageText = '" . $message . "';
                         if (messageBlock) {
                             messageBlock.after('<div id=\'plumbaseMessageBlock\' class=\'message message-notice notice\'><div data-ui-id=\'messages-message-notice\'>'
                                 + messageText
                                 + '</div></div><br/>'
                             );
                         }
                     });
                </script>";
            }
        }

        return $html . $messageHtml;
    }

    /**
     * Check environment
     * @deprecated since 2.5.0
     * @see \Plumrocket\Base\Model\IsModuleInMarketplace::execute
     *
     * @param  string $handle
     * @return boolean
     */
    protected function isMarketplace($handle)
    {
        return $this->isModuleInMarketplace->execute($handle);
    }

    /**
     * @deprecated since 2.5.0
     *
     * @return string
     */
    protected function _getAdditionalInfoHtml()
    {
        return '';
    }

    /**
     * @deprecated since 2.5.0
     *
     * @return string
     */
    protected function _getIHtml()
    {
        return '';
    }
}
