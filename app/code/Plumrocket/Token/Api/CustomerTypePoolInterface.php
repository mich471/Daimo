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

namespace Plumrocket\Token\Api;

/**
 * Interface CustomerTypePoolInterface
 *
 * Collect \Plumrocket\Token\Api\TypeInterface via id
 */
interface CustomerTypePoolInterface
{
    /**
     * CustomerTypePoolInterface constructor.
     *
     * @param \Plumrocket\Token\Api\TypeInterface[] $types
     */
    public function __construct(array $types = []);

    /**
     * @return \Plumrocket\Token\Api\TypeInterface[]
     */
    public function getList() : array;

    /**
     * @param string $typeKey
     * @return \Plumrocket\Token\Api\TypeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByType(string $typeKey) : \Plumrocket\Token\Api\TypeInterface;
}
