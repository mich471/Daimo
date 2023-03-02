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
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\StoreManagerInterface;
use Plumrocket\CookieConsent\Controller\Adminhtml\AbstractItem;

/**
 * @since 1.0.0
 */
class Edit extends AbstractItem
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Plumrocket\CookieConsent\Controller\Adminhtml\Item\Builder
     */
    private $cookieBuilder;

    /**
     * Edit constructor.
     *
     * @param \Magento\Backend\App\Action\Context                         $context
     * @param \Magento\Store\Model\StoreManagerInterface                  $storeManager
     * @param \Plumrocket\CookieConsent\Controller\Adminhtml\Item\Builder $cookieBuilder
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Builder $cookieBuilder
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->cookieBuilder = $cookieBuilder;
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

        $cookie = $this->cookieBuilder->build($this->getRequest());

        if ($id && ! $cookie->getId()) {
            $this->messageManager->addErrorMessage(__('This cookie cookie no longer exists.'));

            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->initBreadcrumbAndMenu($resultPage)->addBreadcrumb(
            $id ? __('Edit Cookie') : __('New Cookie'),
            $id ? __('Edit Cookie') : __('New Cookie')
        );

        $title = $resultPage->getConfig()->getTitle();

        $title->prepend(__('Cookie Category'));
        $title->prepend($cookie->getId() ? __('Edit Cookie "%1"', $cookie->getName()) : __('New Cookie'));

        return $resultPage;
    }
}
