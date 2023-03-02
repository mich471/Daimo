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
 * @package     Plumrocket_GeoIPLookup
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\GeoIPLookup\Model;

use Magento\Directory\Model\Config\Source\Allregion;
use Magento\Directory\Model\Config\Source\Country;
use Plumrocket\GeoIPLookup\Api\LocationsListInterface;

/**
 * @since 1.2.0
 */
class LocationsList implements LocationsListInterface
{
    /**
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    private $directoryCountry;

    /**
     * @var \Magento\Directory\Model\Config\Source\Allregion
     */
    private $directoryRegion;

    /**
     * LocationsList constructor.
     *
     * @param \Magento\Directory\Model\Config\Source\Country   $directoryCountry
     * @param \Magento\Directory\Model\Config\Source\Allregion $directoryRegion
     */
    public function __construct(
        Country $directoryCountry,
        Allregion $directoryRegion
    ) {
        $this->directoryCountry = $directoryCountry;
        $this->directoryRegion = $directoryRegion;
    }

    /**
     * @inheritDoc
     */
    public function getRegions(): array
    {
        return [
            [
                'value'  => self::ALL,
                'label'  => __('Show to all site visitors'),
            ],
            [
                'value' => self::EU,
                'label' => __('Only visitors from EU countries'),
            ],
            [
                'value' => self::UNKNOWN,
                'label' => __('Unknown'),
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getCountries(): array
    {
        return $this->directoryCountry->toOptionArray(true);
    }

    /**
     * @inheritDoc
     */
    public function getCountryStates(string $countryName): array
    {
        $allRegions = $this->directoryRegion->toOptionArray();

        $result = [];
        foreach ($allRegions as $region) {
            if ($region['label'] === $countryName) {
                $regionOptions = $region['value'];
                $result[] = [
                    'value' => self::ALL,
                    'label' => __('Show to all states')
                ];

                foreach ($regionOptions as $regionOption) {
                    $result[] = [
                        'value' => $regionOption['label'],
                        'label' => $regionOption['label']
                    ];
                }

                break;
            }
        }

        return $result;
    }
}
