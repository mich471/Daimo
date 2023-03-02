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

use Magento\Framework\Exception\LocalizedException;
use Plumrocket\DataPrivacyApi\Api\ConsentLocationTypeInterface;

class Save extends \Plumrocket\GDPR\Controller\Adminhtml\Consent\Location
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $postData = $this->getRequest()->getPostValue();

        if ($postData) {
            $id = $this->getRequest()->getParam('location_id');
            $postData['type'] = ConsentLocationTypeInterface::TYPE_CUSTOM;

            if (empty($postData['location_id'])) {
                $postData['location_id'] = null;
            }

            /** @var \Plumrocket\GDPR\Model\Consent\Location $model */
            $model = $this->consentLocationFactory->create();
            $this->consentLocationResource->load($model, $id);

            if ($id && ! $model->getId()) {
                $this->messageManager->addErrorMessage(__('This consent location no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            if (($model->getId() && $model->isSystem())
                || $model->setData($postData)->isSystem()
            ) {
                $this->dataPersistor->set('consent_location', $postData);
                $this->messageManager->addErrorMessage(
                    $this->getErrorTextForSystemLocation($model->getLocationKey())
                );

                return $resultRedirect->setPath('*/*/edit', ['location_id' => $model->getId()]);
            }

            try {
                $this->consentLocationResource->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the consent location.'));
                $this->dataPersistor->clear('consent_location');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['location_id' => $model->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the consent location.')
                );
            }

            $this->dataPersistor->set('consent_location', $postData);

            return $resultRedirect->setPath('*/*/edit', [
                'location_id' => $this->getRequest()->getParam('location_id')
            ]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
