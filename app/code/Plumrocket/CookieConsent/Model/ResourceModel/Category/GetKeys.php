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
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Model\ResourceModel\Category;

use Magento\Framework\App\ResourceConnection;
use Plumrocket\CookieConsent\Model\ResourceModel\Category;

/**
 * Retrieve keys of created categories
 *
 * @since 1.0.0
 */
class GetKeys
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
     * @return string[]
     */
    public function execute(): array
    {
        $connection = $this->resourceConnection->getConnection();

        $select = $connection
            ->select()
            ->from(
                ['main_table' => $this->resourceConnection->getTableName(Category::MAIN_TABLE_NAME)],
                ['key']
            );

        return $connection->fetchCol($select);
    }
}
