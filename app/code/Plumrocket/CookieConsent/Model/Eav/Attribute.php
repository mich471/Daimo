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

namespace Plumrocket\CookieConsent\Model\Eav;

use Magento\Catalog\Model\Category\Attribute as CategoryAttribute;

/**
 * @since 1.0.0
 */
class Attribute extends \Magento\Catalog\Model\Entity\Attribute
{
    /**
     * Retrieve attribute scope
     *
     * @return string|null
     */
    public function getScope()
    {
        $scope = (int) $this->_getData(CategoryAttribute::KEY_IS_GLOBAL);
        if ($scope === CategoryAttribute::SCOPE_GLOBAL) {
            return CategoryAttribute::SCOPE_GLOBAL_TEXT;
        }
        if ($scope === CategoryAttribute::SCOPE_WEBSITE) {
            return CategoryAttribute::SCOPE_WEBSITE_TEXT;
        }

        return CategoryAttribute::SCOPE_STORE_TEXT;
    }

    /**
     * Set attribute scope
     *
     * @param string $scope
     * @return $this
     */
    public function setScope($scope)
    {
        if ($scope === CategoryAttribute::SCOPE_GLOBAL_TEXT) {
            return $this->setData(CategoryAttribute::KEY_IS_GLOBAL, CategoryAttribute::SCOPE_GLOBAL);
        }
        if ($scope === CategoryAttribute::SCOPE_WEBSITE_TEXT) {
            return $this->setData(CategoryAttribute::KEY_IS_GLOBAL, CategoryAttribute::SCOPE_WEBSITE);
        }
        if ($scope === CategoryAttribute::SCOPE_STORE_TEXT) {
            return $this->setData(CategoryAttribute::KEY_IS_GLOBAL, CategoryAttribute::SCOPE_STORE);
        }

        //Ignore unrecognized scope
        return $this;
    }

    /**
     * Return is attribute global
     *
     * @return integer
     */
    public function getIsGlobal(): int
    {
        return (int) $this->_getData(CategoryAttribute::KEY_IS_GLOBAL);
    }

    /**
     * Retrieve attribute is global scope flag
     *
     * @return bool
     */
    public function isScopeGlobal(): bool
    {
        return $this->getIsGlobal() === CategoryAttribute::SCOPE_GLOBAL;
    }

    /**
     * Retrieve attribute is website scope website
     *
     * @return bool
     */
    public function isScopeWebsite(): bool
    {
        return $this->getIsGlobal() === CategoryAttribute::SCOPE_WEBSITE;
    }

    /**
     * Retrieve attribute is store scope flag
     *
     * @return bool
     */
    public function isScopeStore(): bool
    {
        return !$this->isScopeGlobal() && !$this->isScopeWebsite();
    }
}
