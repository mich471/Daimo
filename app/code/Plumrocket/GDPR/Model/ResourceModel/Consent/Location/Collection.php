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

namespace Plumrocket\GDPR\Model\ResourceModel\Consent\Location;

use Plumrocket\GDPR\Model\Consent\Location as LocationModel;
use Plumrocket\GDPR\Model\ResourceModel\Consent\Location as LocationResourceModel;

/**
 * @method \Plumrocket\GDPR\Api\Data\ConsentLocationInterface[] getItems()
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define collection model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_idFieldName = LocationResourceModel::MAIN_TABLE_ID_FIELD_NAME;
        $this->_init(LocationModel::class, LocationResourceModel::class);
    }

    /**
     * @param bool $visible
     * @return $this
     */
    public function addVisibleFilter(bool $visible = true) : self
    {
        $this->addFieldToFilter('visible', (int)$visible);

        return $this;
    }

    /**
     * @return array
     */
    public function toOptionIdArray() : array
    {
        $options = [];

        foreach ($this->getItems() as $item) {
            $options[] = [
                'value' => $item->getLocationKey(),
                'label' => $item->getName(),
            ];
        }

        return $options;
    }
}
