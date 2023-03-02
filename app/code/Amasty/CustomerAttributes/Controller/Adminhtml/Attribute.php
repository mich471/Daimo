<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (http://www.amasty.com)
 * @package Amasty_CustomerAttributes
 */
namespace Amasty\CustomerAttributes\Controller\Adminhtml;

use Magento\Framework\Controller\Result;
use Magento\Framework\View\Result\PageFactory;

abstract class Attribute extends \Magento\Backend\App\Action
{

    /**
     * @var int
     */
    protected $_entityTypeId;

    /**
     * @var int
     */
    protected $customerAddressEntityTypeId;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        PageFactory $resultPageFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $this->_entityTypeId = \Magento\Customer\Api\CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER;
        $this->customerAddressEntityTypeId = \Magento\Customer\Api\AddressMetadataInterface::ATTRIBUTE_SET_ID_ADDRESS;

        return parent::dispatch($request);
    }

    /**
     * @param \Magento\Framework\Phrase|null $title
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function createActionPage($title = null)
    {
        // @var \Magento\Backend\Model\View\Result\Page $resultPage
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb(__('Customer'), __('Customer'))
            ->addBreadcrumb(__('Customer Attributes'), __('Customer Attributes'))
            ->setActiveMenu('Amasty_CustomerAttributes::attributes');
        if (!empty($title)) {
            $resultPage->addBreadcrumb($title, $title);
        }

        $resultPage->getConfig()->getTitle()->prepend(__('Customer Attributes'));
        return $resultPage;
    }

    /**
     * ACL check
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_CustomerAttributes::attributes_manage');
    }
}
