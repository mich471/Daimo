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

namespace Plumrocket\GeoIPLookup\Model\System\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Plumrocket\GeoIPLookup\Api\LocationsListInterface;

/**
 * @since 1.2.2
 */
class GeoTargetingStates implements OptionSourceInterface
{
    /**
     * @var \Plumrocket\GeoIPLookup\Api\LocationsListInterface
     */
    private $locationsList;

    /**
     * @param \Plumrocket\GeoIPLookup\Api\LocationsListInterface $locationsList
     */
    public function __construct(
        LocationsListInterface $locationsList
    ) {
        $this->locationsList = $locationsList;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->locationsList->getCountryStates('United States');
    }
}
