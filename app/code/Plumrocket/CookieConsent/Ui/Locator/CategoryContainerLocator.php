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

namespace Plumrocket\CookieConsent\Ui\Locator;

use Plumrocket\CookieConsent\Api\Data\CategoryInterface;

/**
 * @since 1.0.0
 */
class CategoryContainerLocator extends AbstractContainerLocator implements CategoryLocatorInterface
{
    /**
     * @inheritDoc
     */
    public function getCategory(): CategoryInterface
    {
        return $this->getModel();
    }

    /**
     * @inheritDoc
     */
    public function setCategory(CategoryInterface $category): CategoryLocatorInterface
    {
        $this->setModel($category);
        return $this;
    }
}
