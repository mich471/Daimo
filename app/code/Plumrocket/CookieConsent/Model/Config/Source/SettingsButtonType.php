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

namespace Plumrocket\CookieConsent\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * @since 1.0.0
 */
class SettingsButtonType implements OptionSourceInterface
{
    const BUTTON = 'button';
    const LINK = 'link';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::BUTTON,
                'label' => __('Button'),
            ],
            [
                'value' => self::LINK,
                'label' => __('Link'),
            ],
        ];
    }
}
