<?php
/**
 * @package     Plumrocket_magento_2_3_6__1
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model\ResourceModel\Consent;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Location extends AbstractDb
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
