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

declare(strict_types=1);

namespace Plumrocket\GDPR\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Plumrocket\DataPrivacy\Helper\Config;
use Plumrocket\GDPR\Helper\Checkboxes;
use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;

class CustomerRegisterSuccess implements ObserverInterface
{

    /**
     * @var \Plumrocket\GDPR\Helper\Checkboxes
     */
    private $checkboxesHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Plumrocket\DataPrivacy\Helper\Config
     */
    private $config;

    /**
     * @param \Plumrocket\GDPR\Helper\Checkboxes    $checkboxesHelper
     * @param \Magento\Framework\Registry           $coreRegistry
     * @param \Plumrocket\DataPrivacy\Helper\Config $config
     */
    public function __construct(
        Checkboxes $checkboxesHelper,
        Registry $coreRegistry,
        Config $config
    ) {
        $this->checkboxesHelper = $checkboxesHelper;
        $this->coreRegistry = $coreRegistry;
        $this->config = $config;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getData('customer');
        /** @var \Magento\Customer\Controller\Account\CreatePost $controller */
        $controller = $observer->getData('account_controller');

        if ($controller && $this->config->isModuleEnabled()) {
            $this->logConsentCheckboxes($customer, $controller->getRequest());
        }
    }

    /**
     * @param $customer
     * @param $request
     * @return $this
     */
    public function logConsentCheckboxes($customer, $request)
    {
        $this->coreRegistry->register('prgdpr_skip_save_consents', 1);

        $consents = $request->getParam('prgdpr_consent', null);

        if ($customer && $customer->getId()) {
            $this->checkboxesHelper->saveMultipleConsents(
                ConsentLocations::REGISTRATION,
                $consents,
                [
                    'customer_id' => $customer->getId(),
                ]
            );
        }

        return $this;
    }
}
