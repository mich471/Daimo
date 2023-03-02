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

namespace Plumrocket\CookieConsent\Controller\Adminhtml\Consent\Log;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultFactory;
use Plumrocket\CookieConsent\Api\Data\ConsentLogInterface;

/**
 * @since 1.0.0
 */
class Index extends Action
{
    const ADMIN_RESOURCE = 'Plumrocket_CookieConsent::consent_log';

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * Index constructor.
     *
     * @param \Magento\Backend\App\Action\Context                   $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
                   ->addBreadcrumb(__('Cookie Consent'), __('Cookie Consent'))
                   ->addBreadcrumb(__('Consent Log'), __('Consent Log'));

        $resultPage->getConfig()->getTitle()->prepend(__('Cookie Consent Log'));
        $this->dataPersistor->clear(ConsentLogInterface::DATA_PERSISTOR_KEY);
        return $resultPage;
    }
}
