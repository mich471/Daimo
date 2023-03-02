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

namespace Plumrocket\Token\Api;

use Magento\Framework\Exception\SecurityViolationException;

interface GenerateHashInterface
{
    /**
     * Generator must throw SecurityViolationException if somebody try generate hash shorter than this number
     */
    const MINIMAL_LENGTH = 8;

    /**
     * @param int $length
     * @return string
     * @throws SecurityViolationException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($length = 32) : string;
}
