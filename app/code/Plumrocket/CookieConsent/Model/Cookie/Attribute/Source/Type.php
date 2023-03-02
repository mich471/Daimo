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
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Model\Cookie\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource as EavAbstractSource;

/**
 * @since 1.0.0
 */
class Type extends EavAbstractSource
{
    const TYPE_FIRST = 'first';
    const TYPE_THIRD = 'third';

    /**
     * @return array
     */
    public function getAllOptions(): array
    {
        return [
            [
                'value' => self::TYPE_FIRST,
                'label' => '1st Party',
            ],
            [
                'value' => self::TYPE_THIRD,
                'label' => '3rd Party',
            ],
        ];
    }
}
