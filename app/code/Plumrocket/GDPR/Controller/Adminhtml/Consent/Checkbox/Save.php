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

use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;

class Save extends \Plumrocket\GDPR\Controller\Adminhtml\Consent\Checkbox
{
    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\Checkbox
     */
    private $checkboxResource;

    /**
     * @var Builder
     */
    private $checkboxBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * Save constructor.
     *
     * @param \Magento\Backend\App\Action\Context                   $context
     * @param \Plumrocket\GDPR\Model\ResourceModel\Checkbox         $checkboxResource
     * @param Builder                                               $checkboxBuilder
     * @param \Magento\Store\Model\StoreManagerInterface            $storeManager
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Plumrocket\GDPR\Model\ResourceModel\Checkbox $checkboxResource,
        \Plumrocket\GDPR\Controller\Adminhtml\Consent\Checkbox\Builder $checkboxBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->checkboxResource = $checkboxResource;
        $this->checkboxBuilder = $checkboxBuilder;
        $this->storeManager = $storeManager;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $storeId = $this->getRequest()->getParam('store', 0);
        $store = $this->storeManager->getStore($storeId);
        $this->storeManager->setCurrentStore($store->getCode());

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $postData = $this->getRequest()->getPostValue();
        $postData = $this->prepareData($postData);
        $id = (int) $this->getRequest()->getParam('id');

        if ($postData) {
            $checkbox = $this->checkboxBuilder->build($this->getRequest());
            $this->dataPersistor->set('prgdpr_checkbox', $postData);

            if ($id && ! $checkbox->getId()) {
                $this->messageManager->addErrorMessage(__('This checkbox no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            try {
                $checkbox->addData($postData);

                /**
                 * Check "Use Default Value" checkboxes values
                 */
                if (isset($postData['use_default']) && !empty($postData['use_default'])) {
                    foreach ($postData['use_default'] as $attributeCode => $attributeValue) {
                        if ($attributeValue) {
                            $checkbox->setData($attributeCode, null);
                        }
                    }
                }

                $this->checkboxResource->save($checkbox);
                $this->messageManager->addSuccessMessage(__('You saved the checkbox.'));
                $this->dataPersistor->clear('prgdpr_checkbox');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['id' => $checkbox->getId(), '_current' => true]
                    );
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the checkbox.')
                );
            }

            $this->dataPersistor->set('prgdpr_checkbox', $postData);

            return $resultRedirect->setPath('*/*/edit', [
                'id' => $this->getRequest()->getParam('id'),
                'store' => $storeId,
            ]);
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareData(array $data)
    {
        $data['location_key'][] = ConsentLocations::MY_ACCOUNT;
        return $data;
    }
}
