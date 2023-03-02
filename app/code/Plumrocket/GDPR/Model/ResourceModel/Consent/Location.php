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

namespace Plumrocket\GDPR\Model\ResourceModel\Consent;

/**
 * @see \Plumrocket\DataPrivacy\Model\ResourceModel\Consent\Location
 * @deprecated since 3.1.0
 * @since 1.0.0
 */
class Location extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Name of Main Table
     */
    const MAIN_TABLE_NAME = 'plumrocket_gdpr_consent_location';

    /**
     * Name of Primary Column
     */
    const MAIN_TABLE_ID_FIELD_NAME = 'location_id';

    /**
     * Form Key
     * Using for Controllers And DataProviders
     */
    const FORM_SESSION_KEY = 'prgdpr_consent_location_form_data';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, self::MAIN_TABLE_ID_FIELD_NAME);
    }

    /**
     * @param $data
     * @return int
     */
    public function createMultipleByData($data)
    {
        return $this->getConnection()->insertMultiple($this->getTable(self::MAIN_TABLE_NAME), $data);
    }

    /**
     * @return string[]
     */
    public function getAllLocationKeys() : array
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable(self::MAIN_TABLE_NAME),
            'location_key'
        );

        return $connection->fetchCol($select);
    }
}
