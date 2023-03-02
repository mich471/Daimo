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

use Plumrocket\CookieConsent\Api\GetCategoryIdByKeyInterface;
use Plumrocket\CookieConsent\Model\ResourceModel\Category;

/**
 * Retrieve category id by its key
 *
 * @since 1.0.0
 */
class GetIdByKey implements GetCategoryIdByKeyInterface
{
    /**
     * @var int[]
     */
    private $ids = [];

    /**
     * @var \Plumrocket\CookieConsent\Model\ResourceModel\Category
     */
    private $category;

    /**
     * GetIdByKey constructor.
     *
     * @param \Plumrocket\CookieConsent\Model\ResourceModel\Category $category
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $key, bool $forceReload = false): int
    {
        if (! isset($this->ids[$key])) {
            $connection = $this->category->getConnection();

            $select = $connection->select()->from($this->category->getEntityTable(), 'entity_id')->where('key = :key');

            $bind = [':key' => $key];

            $this->ids[$key] = (int) $connection->fetchOne($select, $bind);
        }

        return $this->ids[$key];
    }
}
