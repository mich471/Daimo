<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\DataPrivacy\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Export Log resource model.
 */
class DownloadLog extends AbstractDb
{
    /**
     * Name of Main Table
     */
    public const MAIN_TABLE_NAME = 'plumrocket_gdpr_export_log';

    /**
     * Name of Primary Column
     */
    public const ID_FIELD_NAME = 'log_id';

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()// @codingStandardsIgnoreLine we need to extend parent method
    {
        $this->_init(self::MAIN_TABLE_NAME, self::ID_FIELD_NAME);
    }
}
