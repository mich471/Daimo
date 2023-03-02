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

use Magento\Framework\Exception\SecurityViolationException;
use \Plumrocket\Token\Api\GenerateHashInterface;

class GenerateHash implements \Plumrocket\Token\Api\GenerateHashInterface
{
    /**
     * @var \Magento\Framework\Math\Random
     */
    private $mathRandom;

    /**
     * TokenHashGenerator constructor.
     *
     * @param \Magento\Framework\Math\Random $mathRandom
     */
    public function __construct(\Magento\Framework\Math\Random $mathRandom)
    {
        $this->mathRandom = $mathRandom;
    }

    /**
     * @inheritDoc
     */
    public function execute($length = 32) : string
    {
        if ($length < GenerateHashInterface::MINIMAL_LENGTH) {
            throw new SecurityViolationException(
                __('Token cannot be shorter than %1', GenerateHashInterface::MINIMAL_LENGTH)
            );
        }

        return $this->mathRandom->getRandomString($length);
    }
}
