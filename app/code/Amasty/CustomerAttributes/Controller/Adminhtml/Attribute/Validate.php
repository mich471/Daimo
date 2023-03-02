<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (http://www.amasty.com)
 * @package Amasty_CustomerAttributes
 */
namespace Amasty\CustomerAttributes\Controller\Adminhtml\Attribute;

class Validate extends \Amasty\CustomerAttributes\Controller\Adminhtml\Attribute
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    protected $eavAttributeResourceModel;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttributeResourceModel
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttributeResourceModel
    ) {
        parent::__construct($context, $coreRegistry, $resultPageFactory);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
        $this->eavAttributeResourceModel = $eavAttributeResourceModel;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        $response->setError(false);

        $attributeCode = $this->getRequest()->getParam('attribute_code');
        $attributeId = $this->getRequest()->getParam('attribute_id');
        $attribute = $this->eavAttributeResourceModel->loadByCode(
            $this->_entityTypeId,
            $attributeCode
        );
        $addressAttribute = $this->eavAttributeResourceModel->loadByCode(
            $this->customerAddressEntityTypeId,
            $attributeCode
        );

        if (($attribute->getId() || $addressAttribute->getId()) && !$attributeId) {
            if (strlen($this->getRequest()->getParam('attribute_code'))) {
                $response->setMessage(
                    __('An attribute with this code already exists.')
                );
            } else {
                $response->setMessage(
                    __('An attribute with the same code (%1) already exists.', $attributeCode)
                );
            }
            $response->setError(true);
        }
        return $this->resultJsonFactory->create()->setJsonData($response->toJson());
    }
}
