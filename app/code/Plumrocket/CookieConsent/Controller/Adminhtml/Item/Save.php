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

namespace Plumrocket\CookieConsent\Controller\Adminhtml\Item;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Plumrocket\CookieConsent\Api\CookieRepositoryInterface;
use Plumrocket\CookieConsent\Api\Data\CookieInterface;
use Plumrocket\CookieConsent\Controller\Adminhtml\AbstractEavSave;
use Plumrocket\CookieConsent\Model\Cookie\Attribute\DurationDecorator;

class Save extends AbstractEavSave
{
    const ADMIN_RESOURCE = 'Plumrocket_CookieConsent::cookies';

    /**
     * @var Builder
     */
    private $cookieBuilder;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Plumrocket\CookieConsent\Api\CategoryRepositoryInterface
     */
    private $cookieRepository;

    /**
     * @var \Plumrocket\CookieConsent\Model\Cookie\Attribute\DurationDecorator
     */
    private $durationDecorator;

    /**
     * Save constructor.
     *
     * @param \Magento\Backend\App\Action\Context                                $context
     * @param \Plumrocket\CookieConsent\Controller\Adminhtml\Item\Builder        $cookieBuilder
     * @param \Magento\Store\Model\StoreManagerInterface                         $storeManager
     * @param \Magento\Framework\App\Request\DataPersistorInterface              $dataPersistor
     * @param \Plumrocket\CookieConsent\Api\CookieRepositoryInterface            $cookieRepository
     * @param \Plumrocket\CookieConsent\Model\Cookie\Attribute\DurationDecorator $durationDecorator
     */
    public function __construct(
        Context $context,
        Builder $cookieBuilder,
        StoreManagerInterface $storeManager,
        DataPersistorInterface $dataPersistor,
        CookieRepositoryInterface $cookieRepository,
        DurationDecorator $durationDecorator
    ) {
        parent::__construct($context, $storeManager);
        $this->cookieBuilder = $cookieBuilder;
        $this->dataPersistor = $dataPersistor;
        $this->cookieRepository = $cookieRepository;
        $this->durationDecorator = $durationDecorator;
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
            $cookie = $this->cookieBuilder->build($this->getRequest());
            $this->dataPersistor->set(CookieInterface::DATA_PERSISTOR_KEY, $postData);

            if ($id && ! $cookie->getId()) {
                $this->messageManager->addErrorMessage(__('This cookie no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            try {
                $postData = $this->durationDecorator->serializeParams($postData);

                $cookie->addData($postData);

                $this->prepareUseDefault($postData, $cookie);

                $this->cookieRepository->save($cookie);
                $this->messageManager->addSuccessMessage(__('You saved the cookie.'));
                $this->dataPersistor->clear(CookieInterface::DATA_PERSISTOR_KEY);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['id' => $cookie->getId(), '_current' => true]
                    );
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the cookie.')
                );
            }

            $this->dataPersistor->set(CookieInterface::DATA_PERSISTOR_KEY, $postData);

            return $resultRedirect->setPath('*/*/edit', [
                'id' => $this->getRequest()->getParam('id'),
                'store' => $storeId,
            ]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
