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

namespace Plumrocket\GDPR\Model\ResourceModel\Checkbox;

use Plumrocket\GDPR\Api\Data\CheckboxInterface;

/**
 * @method \Plumrocket\GDPR\Model\Checkbox[] getItems()
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $idFieldName = \Plumrocket\GDPR\Model\ResourceModel\Checkbox::ID_FIELD_NAME; // @codingStandardsIgnoreLine

    /**
     * Resource initialization.
     *
     * @return void
     */
    protected function _construct()// @codingStandardsIgnoreLine we need to extend parent method
    {
        $this->_init(
            \Plumrocket\GDPR\Model\Checkbox::class,
            \Plumrocket\GDPR\Model\ResourceModel\Checkbox::class
        );
    }

    /**
     * @inheritDoc
     */
    protected function _afterLoad()
    {
        foreach ($this->getItems() as $checkbox) {
            $checkbox->setLocationKeys(explode(',', $checkbox->getData(CheckboxInterface::LOCATION_KEY)));
        }
        return parent::_afterLoad();
    }
}
