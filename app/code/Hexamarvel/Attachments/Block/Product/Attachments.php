<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Attachments
 */
namespace Hexamarvel\Attachments\Block\Product;

class Attachments extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Product
     */
    protected $_product = null;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Hexamarvel\Attachments\Model\ResourceModel\Attachments\CollectionFactory
     */
    protected $_attachmentFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Hexamarvel\Attachments\Model\ResourceModel\Attachments\CollectionFactory $attachmentFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Hexamarvel\Attachments\Model\ResourceModel\Attachments\CollectionFactory $attachmentFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_storeManager = $storeManager;
        $this->_attachmentFactory = $attachmentFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->httpContext = $httpContext;
        $this->customerSession =$customerSession;
        parent::__construct($context, $data);
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }

        return $this->_product;
    }

    /**
     * @return obj $attachments
     */
    public function getAttachmentsByProduct()
    {
        $product = $this->getProduct();
        $attachments = $this->_attachmentFactory->create()->addFieldToFilter('is_active', 1);

        $attachments->addFieldToFilter(
            'products',
            [
                'like' => '%"'.$product->getId() .'"%'
            ]
        );
        $attachments->addFieldToFilter(
            'customer_group',
            [
                ['null' => true],
                ['finset' => $this->getCustomerGroupId()]
            ]
        );
        $attachments->addFieldToFilter(
            'stores',
            [
                ['eq' => 0],
                ['finset' => $this->_storeManager->getStore()->getId()]
            ]
        );

        return $attachments;
    }

    /**
     * @param string $imageUrl
     * @return string imageUrl
     */
    public function getImage($imageUrl = '')
    {
        if ($imageUrl) {
            return $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . 'hexaattachment/products/icons/' . $imageUrl;
        }

        return '';
    }

    /**
     * @param string $fileName
     * @return string fileUrl
     */
    public function getFileUrl($fileName = '')
    {
        return $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ) . 'hexaattachment/products/attachments/' . $fileName;
    }

    /**
     * @return string configuration
     */
    public function isEnabled()
    {
        return $this->getConfig('hexaattachment/general/enable');
    }

    /**
     * @return string configuration
     */
    public function getTabTitle()
    {
        return $this->getConfig('hexaattachment/general/tab_title');
    }

    /**
     * @return string configuration
     */
    public function getTabSortOrder()
    {
        return $this->getConfig('hexaattachment/general/tab_sortorder');
    }

    /**
     * @return string configuration
     */
    public function getConfiguredArea()
    {
        return $this->getConfig('hexaattachment/general/display_area');
    }

    /**
     * @param string $area
     * @return string title
     */
    public function getAttachmentTitle($area)
    {
        $title = '';
        if ($area != 'producttab') {
            $title = $this->getConfig('hexaattachment/general/attch_title');
        }

        return $title;
    }

    /**
     * @param string $path
     * @return string configuration
     */
    public function getConfig($path)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->_scopeConfig->getValue($path, $storeScope);
    }

    /**
     * @return int customerGroupId
     */
    public function getCustomerGroupId()
    {
        if (!$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH)) {
            return 0;
        }

        return $this->customerSession->getCustomer()->getGroupId();
    }
}
