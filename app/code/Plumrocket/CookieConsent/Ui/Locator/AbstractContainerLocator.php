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
 * @package     Plumrocket_magento2.3.5
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Ui\Locator;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Store\Api\Data\StoreInterface;

/**
 * @since 1.0.0
 */
abstract class AbstractContainerLocator implements LocatorInterface
{
    /**
     * @var string
     */
    private $modelName = 'model';

    /**
     * @var AbstractExtensibleModel
     */
    private $model;

    /**
     * @var StoreInterface
     */
    private $store;

    /**
     * @inheritDoc
     */
    public function getModel(): AbstractExtensibleModel
    {
        if (null !== $this->model) {
            return $this->model;
        }

        throw new NotFoundException(__("The %1 wasn't registered.", $this->modelName));
    }

    /**
     * @inheritDoc
     */
    public function getStore(): StoreInterface
    {
        if (null !== $this->store) {
            return $this->store;
        }

        throw new NotFoundException(__("The store wasn't registered. Verify the store and try again."));
    }

    /**
     * @inheritDoc
     */
    public function setModel(AbstractExtensibleModel $model): LocatorInterface
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setStore(StoreInterface $store): LocatorInterface
    {
        $this->store = $store;
        return $this;
    }
}
