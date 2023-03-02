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

interface CustomerValidatorInterface
{
    /**
     * @param \Plumrocket\Token\Api\Data\CustomerInterface $token
     * @param string                                       $typeKey @since 1.0.3 will be required in next major version
     * @return bool
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    public function validate(CustomerInterface $token, string $typeKey = ''): bool;
}
