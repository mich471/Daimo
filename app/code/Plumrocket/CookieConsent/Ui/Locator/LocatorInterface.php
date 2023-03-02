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

namespace Plumrocket\CookieConsent\Ui\Locator;

use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Store\Api\Data\StoreInterface;

/**
 * @since 1.0.0
 */
interface LocatorInterface
{
    /**
     * @return \Magento\Framework\Model\AbstractExtensibleModel
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getModel(): AbstractExtensibleModel;

    /**
     * @param \Magento\Framework\Model\AbstractExtensibleModel $model
     * @return \Plumrocket\CookieConsent\Ui\Locator\LocatorInterface
     */
    public function setModel(AbstractExtensibleModel $model): LocatorInterface;

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getStore(): StoreInterface;

    /**
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @return \Plumrocket\CookieConsent\Ui\Locator\LocatorInterface
     */
    public function setStore(StoreInterface $store): LocatorInterface;
}
