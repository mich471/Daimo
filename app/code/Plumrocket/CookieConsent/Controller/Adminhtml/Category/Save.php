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
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Controller\Adminhtml\Category;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Plumrocket\CookieConsent\Api\CategoryRepositoryInterface;
use Plumrocket\CookieConsent\Api\Data\CategoryInterface;
use Plumrocket\CookieConsent\Controller\Adminhtml\AbstractEavSave;

class Save extends AbstractEavSave
{
    const ADMIN_RESOURCE = 'Plumrocket_CookieConsent::cookie_categories';

    /**
     * @var Builder
     */
    private $categoryBuilder;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Plumrocket\CookieConsent\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * Save constructor.
     *
     * @param \Magento\Backend\App\Action\Context                             $context
     * @param \Plumrocket\CookieConsent\Controller\Adminhtml\Category\Builder $categoryBuilder
     * @param \Magento\Store\Model\StoreManagerInterface                      $storeManager
     * @param \Magento\Framework\App\Request\DataPersistorInterface           $dataPersistor
     * @param \Plumrocket\CookieConsent\Api\CategoryRepositoryInterface       $categoryRepository
     */
    public function __construct(
        Context $context,
        Builder $categoryBuilder,
        StoreManagerInterface $storeManager,
        DataPersistorInterface $dataPersistor,
        CategoryRepositoryInterface $categoryRepository
    ) {
        parent::__construct($context, $storeManager);
        $this->categoryBuilder = $categoryBuilder;
        $this->dataPersistor = $dataPersistor;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $storeId = $this->initCurrentStore();

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $postData = $this->getRequest()->getPostValue();
        $id = (int) $this->getRequest()->getParam('id');

        if ($postData) {
            $category = $this->categoryBuilder->build($this->getRequest());
            $this->dataPersistor->set(CategoryInterface::DATA_PERSISTOR_KEY, $postData);

            if ($id && ! $category->getId()) {
                $this->messageManager->addErrorMessage(__('This category no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            try {
                $category->addData($postData);

                $this->prepareUseDefault($postData, $category);

                $this->categoryRepository->save($category);
                $this->messageManager->addSuccessMessage(__('You saved the category.'));
                $this->dataPersistor->clear(CategoryInterface::DATA_PERSISTOR_KEY);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['id' => $category->getId(), '_current' => true]
                    );
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the category.')
                );
            }

            $this->dataPersistor->set(CategoryInterface::DATA_PERSISTOR_KEY, $postData);

            return $resultRedirect->setPath('*/*/edit', [
                'id' => $this->getRequest()->getParam('id'),
                'store' => $storeId,
            ]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
