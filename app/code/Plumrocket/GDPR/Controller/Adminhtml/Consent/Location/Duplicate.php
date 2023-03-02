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

class Duplicate extends \Plumrocket\GDPR\Controller\Adminhtml\Consent\Location
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('location_id');

        if ($id) {
            try {
                /** @var \Plumrocket\GDPR\Model\Consent\Location $model */
                $model = $this->consentLocationFactory->create();
                $this->consentLocationResource->load($model, $id);
                $model->setId(null);
                $model->setLocationKey('copy_' . $model->getLocationKey());
                $this->dataPersistor->set('consent_location', $model->getData());

                return $resultRedirect->setPath('*/*/new');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/new', ['location_id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a consent location to duplicate.'));

        return $resultRedirect->setPath('*/*/');
    }
}
