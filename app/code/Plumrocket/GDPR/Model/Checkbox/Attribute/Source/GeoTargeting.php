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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Model\Checkbox\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource as EavAbstractSource;

class GeoTargeting extends EavAbstractSource
{
    /**
     * @var \Plumrocket\GDPR\Model\Config\Source\GeoIPRestrictions
     */
    private $geoIPRestrictions;

    public function __construct(\Plumrocket\GDPR\Model\Config\Source\GeoIPRestrictions $geoIPRestrictions)
    {
        $this->geoIPRestrictions = $geoIPRestrictions;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        return $this->geoIPRestrictions->toOptionArray();
    }
}
