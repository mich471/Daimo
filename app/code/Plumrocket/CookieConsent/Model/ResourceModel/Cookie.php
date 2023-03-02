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

namespace Plumrocket\CookieConsent\Model\ResourceModel;

use Magento\Catalog\Model\ResourceModel\AbstractResource as CatalogAbstractResource;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Plumrocket\CookieConsent\Api\Data\CookieInterface;

/**
 * Export Log resource model.
 */
class Cookie extends CatalogAbstractResource
{
    /**
     * Name of Main Table
     */
    const MAIN_TABLE_NAME = 'pr_cookie_entity';

    /**
     * Name of Primary Column
     */
    const ID_FIELD_NAME = 'entity_id';

    /**
     * @return \Magento\Eav\Model\Entity\Type
     */
    public function getEntityType()
    {
        if (empty($this->_type)) {
            $this->setType(\Plumrocket\CookieConsent\Model\Cookie::ENTITY);
        }

        return parent::getEntityType();
    }

    /**
     * Retrieve customer entity default attributes
     *
     * @return string[]
     */
    protected function _getDefaultAttributes(): array
    {
        return [
            'created_at',
            'updated_at',
        ];
    }

    /**
     * Perform operations before object save
     *
     * @param DataObject|CookieInterface $object
     * @return \Plumrocket\CookieConsent\Model\ResourceModel\Cookie
     * @throws LocalizedException
     */
    protected function _beforeSave(DataObject $object): Cookie
    {
        if (! $this->isUniqueCookieName($object)) {
            throw new LocalizedException(
                __('A cookie with the same name already exists. Please update old cookie or delete it.')
            );
        }
        return $this;
    }

    /**
     * @param \Plumrocket\CookieConsent\Api\Data\CookieInterface $cookie
     * @return bool
     */
    private function isUniqueCookieName(CookieInterface $cookie): bool
    {
        $select = $this->getConnection()->select()
                       ->from($this->getTable(self::MAIN_TABLE_NAME))
                       ->where('name = ?  ', $cookie->getName());

        if ($cookie->getId()) {
            $select->where(self::ID_FIELD_NAME . ' <> ?', $cookie->getId());
        }

        if ($this->getConnection()->fetchRow($select)) {
            return false;
        }

        return true;
    }
}
