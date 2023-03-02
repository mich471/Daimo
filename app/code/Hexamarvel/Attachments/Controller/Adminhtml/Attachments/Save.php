<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Attachments
 */
namespace Hexamarvel\Attachments\Controller\Adminhtml\Attachments;

use Magento\Framework\App\ObjectManager;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Hexamarvel\Attachments\Model\AttachmentsFactory
     */
    protected $attachmentsFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Hexamarvel\Attachments\Model\AttachmentsFactory $attachmentsFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Hexamarvel\Attachments\Model\AttachmentsFactory $attachmentsFactory
    ) {
        parent::__construct($context);
        $this->attachmentsFactory = $attachmentsFactory;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$data) {
            $this->_redirect('*/*/index');
            return;
        }

        try {
            $rowData = $this->attachmentsFactory->create();
            if (isset($data['icon'][0]['name'])) {
                if (isset($data['icon'][0]['tmp_name'])) {
                    $this->imageUploader = ObjectManager::getInstance()->get('Hexamarvel\Attachments\ImageUpload');
                    $this->imageUploader->moveFileFromTmp($data['icon'][0]['name']);
                }

                $data['icon'] = $data['icon'][0]['name'];
            } else {
                $data['icon'] = '';
            }

            if (isset($data['file'][0]['name'])) {
                if (isset($data['file'][0]['tmp_name'])) {
                    $this->imageUploader = ObjectManager::getInstance()->get('Hexamarvel\Attachments\FileUpload');
                    $this->imageUploader->moveFileFromTmp($data['file'][0]['name']);
                }

                $data['file'] = $data['file'][0]['name'];
            }

            $data['customer_group'] = implode(',', $data['customer_group']);
            $data['stores'] = implode(',', $data['stores']);

            $rowData->setData($data);
            if (isset($data['id'])) {
                $rowData->setEntityId($data['id']);
            }

            $rowData->save();
            $this->messageManager->addSuccess(__('Attachment has been successfully saved.'));
            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $rowData->getId()]);
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }

        return $resultRedirect->setPath('*/*/index');
    }
}
