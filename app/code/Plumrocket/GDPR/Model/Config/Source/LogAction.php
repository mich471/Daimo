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
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Model\Config\Source;

class LogAction implements \Magento\Framework\Option\ArrayInterface
{
    const ACTION_ALLOWED = 1;
    const ACTION_DENIED = 0;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ACTION_ALLOWED,
                'label' => __('Allowed'),
            ],
            [
                'value' => self::ACTION_DENIED,
                'label' => __('Denied'),
            ],
        ];
    }

    /**
     * @return array
     */
    public function toOptionAssocArray()
    {
        $assocOptions = [];

        foreach ($this->toOptionArray() as $option) {
            $assocOptions[$option['value']] = $option['label'];
        }

        return $assocOptions;
    }
}
