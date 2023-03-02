<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\DataPrivacy\Model\Consent\Validation;

use Magento\Framework\App\Response\HttpInterface;
use Magento\Framework\Phrase;

/**
 * @since 3.1.0
 */
interface NotAgreedResponseStrategyInterface
{

    /**
     * @param \Magento\Framework\Phrase $errorMessage
     * @return \Plumrocket\DataPrivacy\Model\Consent\Validation\NotAgreedResponseStrategyInterface
     */
    public function setMessage(Phrase $errorMessage): NotAgreedResponseStrategyInterface;

    /**
     * @param \Magento\Framework\App\Response\HttpInterface $response
     * @return \Plumrocket\DataPrivacy\Model\Consent\Validation\NotAgreedResponseStrategyInterface
     */
    public function render(HttpInterface $response): NotAgreedResponseStrategyInterface;
}
