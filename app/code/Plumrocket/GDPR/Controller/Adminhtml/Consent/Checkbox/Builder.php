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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Controller\Adminhtml\Consent\Checkbox;

use Magento\Framework\App\RequestInterface;

class Builder
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Plumrocket\GDPR\Model\CheckboxFactory
     */
    private $checkboxFactory;

    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\Checkbox
     */
    private $checkboxResource;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Store\Model\StoreFactory
     */
    private $storeFactory;

    /**
     * Builder constructor.
     *
     * @param \Magento\Framework\Registry                   $coreRegistry
     * @param \Plumrocket\GDPR\Model\CheckboxFactory        $checkboxFactory
     * @param \Plumrocket\GDPR\Model\ResourceModel\Checkbox $checkboxResource
     * @param \Magento\Store\Model\StoreManagerInterface    $storeManager
     * @param \Magento\Store\Model\StoreFactory             $storeFactory
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Plumrocket\GDPR\Model\CheckboxFactory $checkboxFactory,
        \Plumrocket\GDPR\Model\ResourceModel\Checkbox $checkboxResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Model\StoreFactory $storeFactory
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->checkboxFactory = $checkboxFactory;
        $this->checkboxResource = $checkboxResource;
        $this->storeManager = $storeManager;
        $this->storeFactory = $storeFactory;
    }

    /**
     * Build checkbox based on user request
     *
     * @param RequestInterface $request
     * @return \Plumrocket\GDPR\Model\Checkbox
     */
    public function build(RequestInterface $request)
    {
        $storeId = (int) $request->getParam('store', 0);
        $checkboxId = (int) $request->getParam('id');

        $store = $this->storeManager->getStore($storeId);
        $this->storeManager->setCurrentStore($store->getCode());

        /** @var \Plumrocket\GDPR\Model\Checkbox $checkbox */
        $checkbox = $this->checkboxFactory->create();

        if ($checkboxId) {
            $this->checkboxResource->load($checkbox, $checkboxId);
        }

        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeFactory->create();
        $store->load($storeId);

        $checkbox->setStoreId($store->getId());

        $this->coreRegistry->register('current_store', $store);
        $this->coreRegistry->register('current_pr_checkbox', $checkbox);

        return $checkbox;
    }
}
