<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\DataPrivacy\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Remove Schedule model.
 * @since 3.1.0
 */
class DownloadLog extends AbstractModel
{
    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\DownloadLog::class);
    }
}
