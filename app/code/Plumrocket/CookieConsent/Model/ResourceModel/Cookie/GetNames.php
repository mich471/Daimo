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
 * @package     Plumrocket_magento2.3.5
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Model\ResourceModel\Cookie;

use Magento\Framework\App\ResourceConnection;
use Plumrocket\CookieConsent\Model\ResourceModel\Cookie;

class GetNames
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * GetNames constructor.
     *
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @return array
     */
    public function execute() : array
    {
        $connection = $this->resourceConnection->getConnection();

        $select = $connection
            ->select()
            ->from(
                ['main_table' => $this->resourceConnection->getTableName(Cookie::MAIN_TABLE_NAME)],
                ['name']
            );

        return $connection->fetchCol($select);
    }
}
