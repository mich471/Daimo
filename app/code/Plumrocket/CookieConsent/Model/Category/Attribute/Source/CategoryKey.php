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

namespace Plumrocket\CookieConsent\Model\Category\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource as EavAbstractSource;

/**
 * @since 1.0.0
 */
class CategoryKey extends EavAbstractSource
{
    const KEY_NECESSARY = 'necessary';
    const KEY_PREFERENCES = 'preferences';
    const KEY_STATISTICS = 'statistics';
    const KEY_MARKETING = 'marketing';

    /**
     * @return array
     */
    public function getAllOptions(): array
    {
        return [
            [
                'label' => __('Strictly necessary'),
                'value' => self::KEY_NECESSARY,
            ],
            [
                'label' => __('Preferences'),
                'value' => self::KEY_PREFERENCES,
            ],
            [
                'label' => __('Statistics'),
                'value' => self::KEY_STATISTICS,
            ],
            [
                'label' => __('Marketing'),
                'value' => self::KEY_MARKETING,
            ],
            [
                'label' => __('Custom'),
                'value' => 'custom',
            ],
        ];
    }
}
