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

namespace Plumrocket\GDPR\Controller\Adminhtml\Consent;

/**
 * Class Location
 */
abstract class Location extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Plumrocket_GDPR::consent_location';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Plumrocket\GDPR\Model\Consent\LocationFactory
     */
    protected $consentLocationFactory;

    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\Consent\Location
     */
    protected $consentLocationResource;

    /**
     * Location constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Plumrocket\GDPR\Model\Consent\LocationFactory $consentLocationFactory
     * @param \Plumrocket\GDPR\Model\ResourceModel\Consent\Location $consentLocationResource
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Plumrocket\GDPR\Model\Consent\LocationFactory $consentLocationFactory,
        \Plumrocket\GDPR\Model\ResourceModel\Consent\Location $consentLocationResource
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->dataPersistor = $dataPersistor;
        $this->consentLocationFactory = $consentLocationFactory;
        $this->consentLocationResource = $consentLocationResource;
        parent::__construct($context);
    }

    /**
     * Init Consent Location
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initConsentLocation($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('GDPR'), __('GDPR'))
            ->addBreadcrumb(__('Consent Locations'), __('Consent Locations'));

        return $resultPage;
    }

    /**
     * @param $locationKey
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorTextForSystemLocation($locationKey)
    {
        return __('You can\'t create, edit or remove system consent location "%1".', $locationKey);
    }
}
