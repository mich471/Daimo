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

namespace Plumrocket\Token\Model\ResourceModel\Customer;

use Magento\Framework\App\ResourceConnection;
use Plumrocket\Token\Model\ResourceModel\Customer as TokenResource;

class GetTokenIdByHash
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param string $hash
     * @return int
     */
    public function execute(string $hash) : int
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(
            TokenResource::MAIN_TABLE_NAME
        );

        $select = $connection->select()
            ->from($tableName, TokenResource::MAIN_TABLE_ID_FIELD_NAME)
            ->where('token_hash = :hash');

        $bind = [':hash' => $hash];

        return (int) $connection->fetchOne($select, $bind);
    }
}
