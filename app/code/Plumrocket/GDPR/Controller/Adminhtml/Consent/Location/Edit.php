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

namespace Plumrocket\GDPR\Controller\Adminhtml\Consent\Location;

class Edit extends \Plumrocket\GDPR\Controller\Adminhtml\Consent\Location
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * Index constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Plumrocket\GDPR\Model\Consent\LocationFactory $consentLocationFactory
     * @param \Plumrocket\GDPR\Model\ResourceModel\Consent\Location $consentLocationResource
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Plumrocket\GDPR\Model\Consent\LocationFactory $consentLocationFactory,
        \Plumrocket\GDPR\Model\ResourceModel\Consent\Location $consentLocationResource,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry, $dataPersistor, $consentLocationFactory, $consentLocationResource);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Plumrocket\GDPR\Model\Consent\Location $model */
        $model = $this->consentLocationFactory->create();
        $id = $this->getRequest()->getParam('location_id');

        if ($id) {
            $this->consentLocationResource->load($model, $id);

            if (! $model->getId()) {
                $this->messageManager->addErrorMessage(__('This consent location no longer exists.'));

                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->coreRegistry->register('consent_location', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initConsentLocation($resultPage)->addBreadcrumb(
            $id ? __('Edit Consent Location') : __('New Consent Location'),
            $id ? __('Edit Consent Location') : __('New Consent Location')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Consent Locations'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getName() : __('New Consent Location'));

        return $resultPage;
    }
}
