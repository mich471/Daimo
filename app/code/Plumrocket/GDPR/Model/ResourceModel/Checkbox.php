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
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Model\ResourceModel;

use Magento\Framework\DataObject;
use Plumrocket\GDPR\Api\Data\CheckboxInterface;

/**
 * Export Log resource model.
 */
class Checkbox extends \Magento\Catalog\Model\ResourceModel\AbstractResource
{
    /**
     * Name of Main Table
     */
    const MAIN_TABLE_NAME = 'prgdpr_checkbox_entity';

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
            $this->setType(\Plumrocket\GDPR\Model\Checkbox::ENTITY);
        }

        return parent::getEntityType();
    }

    /**
     * Retrieve customer entity default attributes
     *
     * @return string[]
     */
    protected function _getDefaultAttributes()
    {
        return [
            'created_at',
            'updated_at',
        ];
    }

    /**
     * @param \Plumrocket\GDPR\Api\Data\CheckboxInterface $object
     * @inheritDoc
     */
    protected function _afterLoad(DataObject $object)
    {
        $object->setLocationKeys(explode(',', $object->getData(CheckboxInterface::LOCATION_KEY)));
        return parent::_afterLoad($object);
    }

    /**
     * @param \Plumrocket\GDPR\Api\Data\CheckboxInterface $object
     * @inheritDoc
     */
    protected function _beforeSave(DataObject $object)
    {
        $object->setData(
            CheckboxInterface::LOCATION_KEY,
            implode(',', (array) $object->getData(CheckboxInterface::LOCATION_KEY))
        );
        return parent::_beforeSave($object);
    }
}
