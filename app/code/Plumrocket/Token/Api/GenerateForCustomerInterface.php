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

use Plumrocket\Token\Api\Data\CustomerInterface;

interface GenerateForCustomerInterface
{
    /**
     * Generate token by type and set authentication info
     *
     * @param int    $customerId
     * @param string $email
     * @param string $typeKey
     * @param array  $additionalData
     * @return \Plumrocket\Token\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\SecurityViolationException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(
        int $customerId,
        string $email,
        string $typeKey,
        array $additionalData = []
    ) : CustomerInterface;
}
