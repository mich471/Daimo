<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Attachments
 */
namespace Hexamarvel\Attachments\Controller\Adminhtml\Attachments;

class Index extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Hexamarvel_Attachments::manage_attachments';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory = false;

    /**
     * @var \Magento\Backend\App\Action\Context $context
     * @var \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Hexamarvel_Attachments::attachment'
        )->addBreadcrumb(
            __('Attachment'),
            __('Attachment')
        )->addBreadcrumb(
            __('Manage Attachment'),
            __('Manage Attachment')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Attachments'));

        return $resultPage;
    }
}
