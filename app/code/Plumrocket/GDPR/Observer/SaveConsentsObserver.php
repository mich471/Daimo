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
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Observer;

use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;

class SaveConsentsObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Plumrocket\GDPR\Helper\Checkboxes
     */
    private $checkboxesHelper;

    /**
     * @var \Plumrocket\GDPR\Helper\Data
     */
    private $dataHelper;

    /**
     * @var array
     */
    private $disallowedActions;

    /**
     * SaveConsentsObserver constructor.
     *
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Plumrocket\GDPR\Helper\Checkboxes $checkboxesHelper
     * @param \Plumrocket\GDPR\Helper\Data $dataHelper
     * @param array $disallowedActions
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Plumrocket\GDPR\Helper\Checkboxes $checkboxesHelper,
        \Plumrocket\GDPR\Helper\Data $dataHelper,
        array $disallowedActions = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->checkboxesHelper = $checkboxesHelper;
        $this->dataHelper = $dataHelper;
        $this->disallowedActions = $disallowedActions;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var null|\Magento\Framework\App\RequestInterface $request */
        $request = $observer->getData('request');
        /** @var null|\Magento\Framework\App\Action\AbstractAction $controllerAction */
        $controllerAction = $observer->getData('controller_action');

        if ($this->dataHelper->moduleEnabled()
            && $request
            && $request->isPost()
            && $controllerAction
            && ! in_array($request->getFullActionName(), $this->disallowedActions)
        ) {
            $this->saveConsentsFromRegistry();
        }
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function saveConsentsFromRegistry()
    {
        if (! $this->coreRegistry->registry('prgdpr_skip_save_consents')) {
            $location = $this->coreRegistry->registry('prgdpr_location');
            $consents = $this->coreRegistry->registry('prgdpr_consent');
            $this->checkboxesHelper->saveMultipleConsents($location, $consents);
        }

        $this->coreRegistry->unregister('prgdpr_location');
        $this->coreRegistry->unregister('prgdpr_consent');
        $this->coreRegistry->unregister('prgdpr_skip_save_consents');

        return $this;
    }
}
