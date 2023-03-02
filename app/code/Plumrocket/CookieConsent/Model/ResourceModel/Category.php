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
use Plumrocket\CookieConsent\Api\Data\CategoryInterface;

/**
 * Export Log resource model.
 */
class Category extends CatalogAbstractResource
{
    /**
     * Name of Main Table
     */
    const MAIN_TABLE_NAME = 'pr_cookie_category_entity';

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
            $this->setType(\Plumrocket\CookieConsent\Model\Category::ENTITY);
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
     * @param DataObject|CategoryInterface $object
     * @return \Plumrocket\CookieConsent\Model\ResourceModel\Category
     * @throws LocalizedException
     */
    protected function _beforeSave(DataObject $object): Category
    {
        if (CategoryInterface::ALL_CATEGORIES === $object->getKey()) {
            throw new LocalizedException(
                __('A cookie category key cannot be "all" because it reserved by extension.')
            );
        }

        if (! $this->isUniqueKey($object)) {
            throw new LocalizedException(
                __('A cookie category with the same key already exists.')
            );
        }
        return $this;
    }

    /**
     * Check if key is unique.
     *
     * @param \Plumrocket\CookieConsent\Api\Data\CategoryInterface $category
     * @return bool
     */
    private function isUniqueKey(CategoryInterface $category): bool
    {
        $select = $this->getConnection()->select()
                       ->from($this->getTable(self::MAIN_TABLE_NAME))
                       ->where('\'key\' = ?  ', $category->getKey());

        if ($category->getId()) {
            $select->where(self::ID_FIELD_NAME . ' <> ?', $category->getId());
        }

        if ($this->getConnection()->fetchRow($select)) {
            return false;
        }

        return true;
    }
}
