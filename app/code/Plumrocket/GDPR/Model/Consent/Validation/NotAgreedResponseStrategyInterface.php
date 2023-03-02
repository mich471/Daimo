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
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Model\Consent\Validation;

interface NotAgreedResponseStrategyInterface
{
    /**
     * @param \Magento\Framework\Phrase $errorMessage
     * @return NotAgreedResponseStrategyInterface
     */
    public function setMessage(\Magento\Framework\Phrase $errorMessage) : self;

    /**
     * @param \Magento\Framework\App\ResponseInterface $response
     * @return NotAgreedResponseStrategyInterface
     */
    public function render(\Magento\Framework\App\ResponseInterface $response) : self;
}
