<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Attachments
 */
namespace Hexamarvel\Attachments\Controller\Adminhtml\Attachments;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Hexamarvel\Attachments\Model\AttachmentsFactory
     */
    private $attachmentFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Hexamarvel\Attachments\Model\AttachmentsFactory $attachmentFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Hexamarvel\Attachments\Model\AttachmentsFactory $attachmentFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->attachmentFactory = $attachmentFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->attachmentFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Attachment no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Hexamarvel_Attachments::attachment'
        )->addBreadcrumb(
            $id ? __('Edit Attachment') : __('New Attachment'),
            $id ? __('Edit Attachment') : __('New Attachment')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Attachments'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getName() : __('New Attachment'));
        return $resultPage;
    }
}
