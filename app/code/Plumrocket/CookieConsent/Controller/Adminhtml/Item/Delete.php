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
use Plumrocket\CookieConsent\Api\CookieRepositoryInterface;
use Plumrocket\CookieConsent\Controller\Adminhtml\AbstractItem;

/**
 * @since 1.0.0
 */
class Delete extends AbstractItem
{
    /**
     * @var \Plumrocket\CookieConsent\Api\CookieRepositoryInterface
     */
    private $cookieRepository;

    /**
     * @param \Magento\Backend\App\Action\Context                     $context
     * @param \Plumrocket\CookieConsent\Api\CookieRepositoryInterface $cookieRepository
     */
    public function __construct(
        Context $context,
        CookieRepositoryInterface $cookieRepository
    ) {
        parent::__construct($context);
        $this->cookieRepository = $cookieRepository;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int) $this->getRequest()->getParam('id');

        if ($id) {
            try {
                $this->cookieRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the cookie.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a cookie to delete.'));

        return $resultRedirect->setPath('*/*/');
    }
}
