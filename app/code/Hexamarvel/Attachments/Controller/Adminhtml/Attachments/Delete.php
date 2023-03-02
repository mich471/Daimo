<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Attachments
 */
namespace Hexamarvel\Attachments\Controller\Adminhtml\Attachments;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Hexamarvel\Attachments\Model\AttachmentsFactory
     */
    private $attachmentFactory;

    /**
     * @param \Magento\Backend\App\Action\Context
     * @param \Hexamarvel\Attachments\Model\AttachmentsFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Hexamarvel\Attachments\Model\AttachmentsFactory $attachmentFactory
    ) {
        parent::__construct($context);
        $this->attachmentFactory = $attachmentFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = $this->attachmentFactory->create();
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('You deleted the Attachment.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a Attachment to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
