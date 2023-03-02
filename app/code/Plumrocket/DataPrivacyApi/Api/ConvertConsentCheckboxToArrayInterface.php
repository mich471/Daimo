<?php
/**
 * @package     Plumrocket_DataPrivacyApi
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\DataPrivacyApi\Api;

use Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface;

/**
 * @since 2.0.0
 */
interface ConvertConsentCheckboxToArrayInterface
{

    /**
     * @param \Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface $checkbox
     * @return array
     */
    public function execute(CheckboxInterface $checkbox): array;
}
