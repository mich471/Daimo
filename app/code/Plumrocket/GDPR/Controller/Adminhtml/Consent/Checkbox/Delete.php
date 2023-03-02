<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\GDPR\Controller\Adminhtml\Consent\Checkbox;

class Delete extends \Plumrocket\GDPR\Controller\Adminhtml\Consent\Checkbox
{
    /**
     * @var \Plumrocket\DataPrivacyApi\Api\CheckboxRepositoryInterface
     */
    private $checkboxRepository;

    /**
     * @param \Magento\Backend\App\Action\Context                        $context
     * @param \Plumrocket\DataPrivacyApi\Api\CheckboxRepositoryInterface $checkboxRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Plumrocket\DataPrivacyApi\Api\CheckboxRepositoryInterface $checkboxRepository
    ) {
        parent::__construct($context);
        $this->checkboxRepository = $checkboxRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('entity_id');

        if ($id) {
            try {
                $this->checkboxRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the checkbox.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a checkbox to delete.'));

        return $resultRedirect->setPath('*/*/');
    }
}
