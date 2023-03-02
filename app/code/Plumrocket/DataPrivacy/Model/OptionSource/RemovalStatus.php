<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model\OptionSource;

use Plumrocket\Base\Model\OptionSource\AbstractSource;

/**
 * @since 3.1.0
 */
class RemovalStatus extends AbstractSource
{
    public const PENDING   = 'pending';
    public const CANCELLED = 'cancelled';
    public const COMPLETED = 'completed';

    /**
     * Get statuses.
     *
     * @return array
     */
    public function toOptionHash(): array
    {
        return [
            self::PENDING   => __('Pending'),
            self::CANCELLED => __('Cancelled'),
            self::COMPLETED => __('Completed'),
        ];
    }
}
