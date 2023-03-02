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

namespace Plumrocket\CookieConsent\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class CategoryStatus implements OptionSourceInterface
{
    const ENABLED = 1;
    const DISABLED = 0;
    const COMPLETED = 'completed';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ENABLED,
                'label' => __('Enabled'),
            ],
            [
                'value' => self::DISABLED,
                'label' => __('Disabled'),
            ],
        ];
    }
}
