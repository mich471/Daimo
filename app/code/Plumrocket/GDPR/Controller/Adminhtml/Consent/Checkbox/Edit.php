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

namespace Plumrocket\GDPR\Controller\Adminhtml\Consent\Checkbox;

class Edit extends \Plumrocket\GDPR\Controller\Adminhtml\Consent\Checkbox
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Builder
     */
    private $checkboxBuilder;

    /**
     * Edit constructor.
     *
     * @param \Magento\Backend\App\Action\Context        $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Builder                                    $checkboxBuilder
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Plumrocket\GDPR\Controller\Adminhtml\Consent\Checkbox\Builder $checkboxBuilder
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->checkboxBuilder = $checkboxBuilder;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        $id = (int) $this->getRequest()->getParam('id');

        $store = $this->storeManager->getStore($storeId);
        $this->storeManager->setCurrentStore($store->getCode());

        $checkbox = $this->checkboxBuilder->build($this->getRequest());

        if ($id && ! $checkbox->getId()) {
            $this->messageManager->addErrorMessage(__('This checkbox no longer exists.'));

            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);

        $this->initConsentLocation($resultPage)->addBreadcrumb(
            $id ? __('Edit Checkbox') : __('New Checkbox'),
            $id ? __('Edit Checkbox') : __('New Checkbox')
        );

        $title = $resultPage->getConfig()->getTitle();

        $title->prepend(__('Checkbox'));
        $title->prepend($checkbox->getId()
                            ? __('Edit Checkbox (ID: %1)', $checkbox->getId())
                            : __('New Checkbox'));

        return $resultPage;
    }
}
