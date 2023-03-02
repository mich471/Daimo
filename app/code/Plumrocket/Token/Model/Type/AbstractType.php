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

namespace Plumrocket\Token\Model\Type;

use Plumrocket\Token\Api\TypeInterface;

/**
 * Class AbstractType
 *
 * Use this class for simplify other types
 * You must declare constants
 */
abstract class AbstractType implements TypeInterface
{
    const KEY = '';
    const LIFETIME_DAYS = 0;

    /**
     * @return string
     */
    public function getKey(): string
    {
        return static::KEY;
    }

    /**
     * Retrieve time in seconds
     *
     * @return int
     */
    public function getLifetime(): int
    {
        return strtotime("{$this->getLifetimeDays()} day", 0);
    }

    /**
     * Retrieve time in days
     *
     * @return int
     */
    public function getLifetimeDays(): int
    {
        return static::LIFETIME_DAYS;
    }
}
