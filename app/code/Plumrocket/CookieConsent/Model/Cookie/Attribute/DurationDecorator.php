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

namespace Plumrocket\CookieConsent\Model\Cookie\Attribute;

use Magento\Framework\Data\OptionSourceInterface;
use Plumrocket\CookieConsent\Api\Data\CookieInterface;

class DurationDecorator implements OptionSourceInterface
{
    const PERIOD_DAYS = 'days';
    const PERIOD_HOURS = 'hours';
    const PERIOD_MINUTES = 'minutes';
    const PERIOD_SECONDS = 'seconds';

    const DURATION_PERIOD = 'duration-period';

    const PERIODS = [
        self::PERIOD_DAYS => 86400,
        self::PERIOD_HOURS => 3600,
        self::PERIOD_MINUTES => 60,
    ];

    /**
     * Serialize two duration fields into one
     *
     * @param array $data
     * @return array
     */
    public function serializeParams(array $data): array
    {
        if ($data[CookieInterface::DURATION]) {
            $duration = $data[CookieInterface::DURATION];
            $period = $data[self::DURATION_PERIOD];
            switch ($period) {
                case self::PERIOD_DAYS:
                    $data[CookieInterface::DURATION] = self::PERIODS[self::PERIOD_DAYS] * $duration;
                    break;
                case self::PERIOD_HOURS:
                    $data[CookieInterface::DURATION] = self::PERIODS[self::PERIOD_HOURS] * $duration;
                    break;
                case self::PERIOD_MINUTES:
                    $data[CookieInterface::DURATION] = self::PERIODS[self::PERIOD_MINUTES] * $duration;
                    break;
                case self::PERIOD_SECONDS:
                    $data[CookieInterface::DURATION] = $duration;
                    break;
            }
        }

        unset($data[self::DURATION_PERIOD]);

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    public function unserializeParams(array $data): array
    {
        return array_merge($data, $this->getValues(
            (int) ($data[CookieInterface::DURATION] ?? 0)
        ));
    }

    /**
     * Calculate values for 'duration' and 'duration-select'
     *
     * @param int $durationInSeconds
     * @return array
     */
    private function getValues(int $durationInSeconds): array
    {
        if ($durationInSeconds) {
            foreach (self::PERIODS as $optionName => $periodInSeconds) {
                if (! ($durationInSeconds % $periodInSeconds)) {
                    return [
                        CookieInterface::DURATION => $durationInSeconds / $periodInSeconds,
                        self::DURATION_PERIOD     => $optionName,
                    ];
                }
            }

            return [
                CookieInterface::DURATION => $durationInSeconds,
                self::DURATION_PERIOD     => self::PERIOD_SECONDS,
            ];
        }

        return [
            CookieInterface::DURATION => 0,
            self::DURATION_PERIOD     => self::PERIOD_DAYS,
        ];
    }

    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::PERIOD_SECONDS,
                'label' => __('seconds')
            ],
            [
                'value' => self::PERIOD_MINUTES,
                'label' => __('minutes')
            ],
            [
                'value' => self::PERIOD_HOURS,
                'label' => __('hours')
            ],
            [
                'value' => self::PERIOD_DAYS,
                'label' => __('days')
            ],
        ];
    }
}
