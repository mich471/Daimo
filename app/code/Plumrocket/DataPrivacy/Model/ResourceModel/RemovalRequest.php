<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\DataPrivacy\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Removal Requests resource model.
 *
 * @since 3.1.0
 */
class RemovalRequest extends AbstractDb
{
    /**
     * Name of Main Table
     */
    public const MAIN_TABLE_NAME = 'plumrocket_gdpr_removal_requests';

    /**
     * Name of Primary Column
     */
    public const ID_FIELD_NAME = 'request_id';

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, self::ID_FIELD_NAME);
    }
}
