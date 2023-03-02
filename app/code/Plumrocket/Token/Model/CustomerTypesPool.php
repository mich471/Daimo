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

namespace Plumrocket\Token\Model;

class CustomerTypesPool implements \Plumrocket\Token\Api\CustomerTypePoolInterface
{
    /**
     * @var array
     */
    private $types;

    /**
     * CustomerTypesPool constructor.
     *
     * @param \Plumrocket\Token\Api\TypeInterface[] $types
     */
    public function __construct(array $types = [])
    {
        $this->types = $types;
    }

    /**
     * @return \Plumrocket\Token\Api\TypeInterface[]
     */
    public function getList() : array
    {
        return $this->types;
    }

    /**
     * @param string $typeKey
     * @return \Plumrocket\Token\Api\TypeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByType(string $typeKey) : \Plumrocket\Token\Api\TypeInterface
    {
        if (isset($this->types[$typeKey])) {
            return $this->types[$typeKey];
        }

        throw new \Magento\Framework\Exception\NoSuchEntityException(
            __('Token type with key "%1" not found.', $typeKey)
        );
    }
}
