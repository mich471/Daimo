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

namespace Plumrocket\GeoIPLookup\Model\System\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Plumrocket\GeoIPLookup\Helper\Config;
use Plumrocket\GeoIPLookup\Model\LocationsList;

/**
 * @since 1.2.2
 */
class GeoTargeting implements OptionSourceInterface
{
    /**
     * @var \Plumrocket\GeoIPLookup\Helper\Config
     */
    private $config;

    /**
     * @var \Plumrocket\GeoIPLookup\Model\LocationsList
     */
    private $locationsList;

    /**
     * @param \Plumrocket\GeoIPLookup\Helper\Config       $config
     * @param \Plumrocket\GeoIPLookup\Model\LocationsList $locationsList
     */
    public function __construct(
        Config $config,
        LocationsList $locationsList
    ) {
        $this->config = $config;
        $this->locationsList = $locationsList;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $regions = $this->locationsList->getRegions();
        $countries = $this->locationsList->getCountries();

        if (! $this->config->isConfiguredForUse()) {
            $regions = array_map([$this, 'addDisabledAttribute'], $regions);
            $countries = array_map([$this, 'addDisabledAttribute'], $countries);
        }

        return [
            [
                'label' => __('By Region'),
                'value' => $regions,
            ],
            [
                'label' => __('By Country'),
                'value' => $countries,
            ],
        ];
    }

    /**
     * @param array $option
     * @return mixed
     */
    public function addDisabledAttribute($option)
    {
        if (LocationsList::ALL !== $option['value']) {
            $option['params']['disabled'] = 'disabled';
        }

        return $option;
    }
}
