<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model\Consent\Checkbox;

use Plumrocket\DataPrivacyApi\Api\Data;

/**
 * @since 3.1.0
 */
class IsAlreadyChecked implements \Plumrocket\DataPrivacyApi\Api\IsAlreadyCheckedCheckboxInterface
{

    public function execute(Data\CheckboxInterface $checkbox, int $customerId = 0, $checkVersion = true): bool
    {
        return $checkbox->isAlreadyChecked($customerId, $checkVersion);
    }
}
