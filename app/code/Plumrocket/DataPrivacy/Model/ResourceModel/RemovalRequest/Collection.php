<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Plumrocket\DataPrivacy\Model\RemovalRequest as RemovalRequestModel;
use Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest as RemovalRequestResourceModel;

/**
 * Removal Requests collection.
 *
 * @method RemovalRequestModel[] getItems()
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $idFieldName = 'request_id';

    /**
     * Resource initialization.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(RemovalRequestModel::class, RemovalRequestResourceModel::class);
    }
}
