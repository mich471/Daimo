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

namespace Plumrocket\GDPR\Block\Adminhtml\Consent\Location\Edit;

class GenericButton
{
    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @var \Plumrocket\GDPR\Model\Consent\LocationFactory
     */
    protected $consentLocationFactory;

    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\Consent\Location
     */
    protected $consentLocationResource;

    /**
     * GenericButton constructor.
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Plumrocket\GDPR\Model\Consent\LocationFactory $consentLocationFactory
     * @param \Plumrocket\GDPR\Model\ResourceModel\Consent\Location $consentLocationResource
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Plumrocket\GDPR\Model\Consent\LocationFactory $consentLocationFactory,
        \Plumrocket\GDPR\Model\ResourceModel\Consent\Location $consentLocationResource
    ) {
        $this->context = $context;
        $this->consentLocationFactory = $consentLocationFactory;
        $this->consentLocationResource = $consentLocationResource;
    }

    /**
     * @return \Plumrocket\GDPR\Model\Consent\Location
     */
    public function getLocationId()
    {
        /** @var \Plumrocket\GDPR\Model\Consent\Location $consentLocation */
        $consentLocation = $this->consentLocationFactory->create();
        $id = $this->context->getRequest()->getParam('location_id');
        $this->consentLocationResource->load($consentLocation, $id);

        return $consentLocation->getId();
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
