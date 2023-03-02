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

namespace Plumrocket\DataPrivacy\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Plumrocket\DataPrivacy\Helper\Config;
use Plumrocket\GDPR\Helper\Checkboxes;
use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;

/**
 * @since 3.0.2
 */
class NewsletterSubscriberSaveAfterObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Plumrocket\GDPR\Helper\Checkboxes
     */
    private $checkboxesHelper;

    /**
     * @var \Plumrocket\DataPrivacy\Helper\Config
     */
    private $config;

    /**
     * @var string[]
     */
    private $createCustomerFullActionPaths;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Registry             $coreRegistry
     * @param \Plumrocket\GDPR\Helper\Checkboxes      $checkboxesHelper
     * @param \Plumrocket\DataPrivacy\Helper\Config   $config
     * @param array                                   $createCustomerFullActionPaths
     */
    public function __construct(
        RequestInterface $request,
        Registry $coreRegistry,
        Checkboxes $checkboxesHelper,
        Config $config,
        array $createCustomerFullActionPaths = []
    ) {
        $this->request = $request;
        $this->checkboxesHelper = $checkboxesHelper;
        $this->coreRegistry = $coreRegistry;
        $this->config = $config;
        $this->createCustomerFullActionPaths = array_keys(array_filter($createCustomerFullActionPaths));
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var null|\Magento\Newsletter\Model\Subscriber $subscriber */
        $subscriber = $observer->getData('data_object');
        if ($subscriber
            && $this->config->isModuleEnabled()
            && ! $this->isRegistration()
        ) {
            $this->saveSubscriberConsents($subscriber);
        }
    }

    /**
     * @param $subscriber
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function saveSubscriberConsents($subscriber): self
    {
        if (! $this->coreRegistry->registry('prgdpr_skip_save_consents')) {
            $this->coreRegistry->register('prgdpr_skip_save_consents', 1);
        }

        if ($subscriber && $subscriber->isSubscribed()) {
            $this->checkboxesHelper->saveMultipleConsents(
                ConsentLocations::NEWSLETTER,
                $this->request->getParam('prgdpr_consent')
            );
        }

        return $this;
    }

    /**
     * @return bool
     */
    private function isRegistration(): bool
    {
        return in_array($this->request->getFullActionName(), $this->createCustomerFullActionPaths, true);
    }
}
