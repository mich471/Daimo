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

namespace Plumrocket\Token\Api\Data;

/**
 * Interface TokenInterface
 *
 * Represent customer data that connected to token
 */
interface CustomerInterface
{
    /**
     * @return int|null
     */
    public function getId();

    /**
     * @return string
     */
    public function getTypeKey() : string;

    /**
     * @return string
     */
    public function getHash() : string;

    /**
     * @return int
     */
    public function getCustomerId() : int;

    /**
     * @return string
     */
    public function getEmail() : string;

    /**
     * @return string
     */
    public function getCreateAt() : string;

    /**
     * @return string
     */
    public function getExpireAt() : string;

    /**
     * @param string $key
     * @return mixed
     */
    public function getAdditionalData(string $key = '');

    /**
     * @param string $typeKey
     * @return \Plumrocket\Token\Api\Data\CustomerInterface
     */
    public function setTypeKey(string $typeKey) : \Plumrocket\Token\Api\Data\CustomerInterface;

    /**
     * @param string $hash
     * @return \Plumrocket\Token\Api\Data\CustomerInterface
     */
    public function setHash(string $hash) : \Plumrocket\Token\Api\Data\CustomerInterface;

    /**
     * @param int $customerId
     * @return \Plumrocket\Token\Api\Data\CustomerInterface
     */
    public function setCustomerId(int $customerId) : \Plumrocket\Token\Api\Data\CustomerInterface;

    /**
     * @param string $email
     * @return \Plumrocket\Token\Api\Data\CustomerInterface
     */
    public function setEmail(string $email) : \Plumrocket\Token\Api\Data\CustomerInterface;

    /**
     * @param string $createAt
     * @return \Plumrocket\Token\Api\Data\CustomerInterface
     */
    public function setCreateAt(string $createAt) : \Plumrocket\Token\Api\Data\CustomerInterface;

    /**
     * @param string $expireAt
     * @return \Plumrocket\Token\Api\Data\CustomerInterface
     */
    public function setExpireAt(string $expireAt) : \Plumrocket\Token\Api\Data\CustomerInterface;

    /**
     * @param array $data
     * @return \Plumrocket\Token\Api\Data\CustomerInterface
     */
    public function setAdditionalData(array $data) : \Plumrocket\Token\Api\Data\CustomerInterface;
}
