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

namespace Plumrocket\GDPR\Model\Locator;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
use Magento\Store\Api\Data\StoreInterface;

class RegistryLocator implements LocatorInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ProductInterface
     */
    private $checkbox;

    /**
     * @var StoreInterface
     */
    private $store;

    /**
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     * @throws NotFoundException
     */
    public function getCheckbox()
    {
        if (null !== $this->checkbox) {
            return $this->checkbox;
        }

        if ($product = $this->registry->registry('current_pr_checkbox')) {
            return $this->checkbox = $product;
        }

        throw new NotFoundException(__("The product wasn't registered."));
    }

    /**
     * {@inheritdoc}
     * @throws NotFoundException
     */
    public function getStore()
    {
        if (null !== $this->store) {
            return $this->store;
        }

        if ($store = $this->registry->registry('current_store')) {
            return $this->store = $store;
        }

        throw new NotFoundException(__("The store wasn't registered. Verify the store and try again."));
    }
}
