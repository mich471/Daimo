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
 * @package     Plumrocket_Token
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Token\Model\ResourceModel;

class Customer extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Name of Main Table
     */
    const MAIN_TABLE_NAME = 'plumrocket_token';

    /**
     * Name of Primary Column
     */
    const MAIN_TABLE_ID_FIELD_NAME = 'token_id';

    /**
     * @inheritDoc
     */
    protected $_serializableFields = ['additional_data' => [null, []]];

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, self::MAIN_TABLE_ID_FIELD_NAME);
    }
}
