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

namespace Plumrocket\CookieConsent\Model\ResourceModel\Cookie;

use Magento\Framework\App\ResourceConnection;
use Plumrocket\CookieConsent\Model\ResourceModel\Cookie;

class GetCategoryKey
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var string[]
     */
    private $categoryKey = [];

    /**
     * GetIdByName constructor.
     *
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param string $name
     * @param bool   $forceReload
     * @return string
     */
    public function execute(string $name, bool $forceReload = false): string
    {
        if (! isset($this->categoryKey[$name]) || $forceReload) {
            $connection = $this->resourceConnection->getConnection();

            $select = $connection
                ->select()
                ->from($this->resourceConnection->getTableName(Cookie::MAIN_TABLE_NAME), 'category_key')
                ->where('name = :name');

            $bind = [':name' => $name];

            $this->categoryKey[$name] = (string) $connection->fetchOne($select, $bind);
        }

        return $this->categoryKey[$name];
    }
}
