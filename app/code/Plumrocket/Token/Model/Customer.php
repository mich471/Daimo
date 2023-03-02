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

class Customer extends \Magento\Framework\Model\AbstractModel implements \Plumrocket\Token\Api\Data\CustomerInterface
{
    /**
     * Initialize resources
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Plumrocket\Token\Model\ResourceModel\Customer::class);
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        $id = parent::getId();
        return $id ? (int) $id : null;
    }

    /**
     * @inheritDoc
     */
    public function getTypeKey() : string
    {
        return (string) $this->_getData('type_key');
    }

    /**
     * @inheritDoc
     */
    public function getHash() : string
    {
        return (string) $this->_getData('token_hash');
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId() : int
    {
        return (int) $this->_getData('customer_id');
    }

    /**
     * @inheritDoc
     */
    public function getEmail() : string
    {
        return (string) $this->_getData('email');
    }

    /**
     * @inheritDoc
     */
    public function getCreateAt() : string
    {
        return (string) $this->_getData('create_at');
    }

    /**
     * @inheritDoc
     */
    public function getExpireAt() : string
    {
        return (string) $this->_getData('expire_at');
    }

    /**
     * @inheritDoc
     */
    public function getAdditionalData(string $key = '')
    {
        if ($key) {
            return $this->getDataByPath("additional_data/{$key}");
        }

        return $this->_getData('additional_data');
    }

    /**
     * @inheritDoc
     */
    public function setTypeKey(string $typeKey) : \Plumrocket\Token\Api\Data\CustomerInterface
    {
        return $this->setData('type_key', $typeKey);
    }

    /**
     * @inheritDoc
     */
    public function setHash(string $hash) : \Plumrocket\Token\Api\Data\CustomerInterface
    {
        return $this->setData('token_hash', $hash);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId(int $customerId) : \Plumrocket\Token\Api\Data\CustomerInterface
    {
        return $this->setData('customer_id', $customerId);
    }

    /**
     * @inheritDoc
     */
    public function setEmail(string $email) : \Plumrocket\Token\Api\Data\CustomerInterface
    {
        return $this->setData('email', $email);
    }

    /**
     * @inheritDoc
     */
    public function setCreateAt(string $createAt) : \Plumrocket\Token\Api\Data\CustomerInterface
    {
        return $this->setData('create_at', $createAt);
    }

    /**
     * @inheritDoc
     */
    public function setExpireAt(string $expireAt) : \Plumrocket\Token\Api\Data\CustomerInterface
    {
        return $this->setData('expire_at', $expireAt);
    }

    /**
     * @inheritDoc
     */
    public function setAdditionalData(array $data) : \Plumrocket\Token\Api\Data\CustomerInterface
    {
        return $this->setData('additional_data', $data);
    }
}
