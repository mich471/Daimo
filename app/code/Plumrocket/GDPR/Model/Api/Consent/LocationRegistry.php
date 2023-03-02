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

namespace Plumrocket\GDPR\Model\Api\Consent;

class LocationRegistry implements \Plumrocket\GDPR\Api\ConsentLocationRegistryInterface
{
    /**
     * @var array
     */
    private $list;

    /**
     * AdditionalLocationRegistry constructor.
     *
     * @param array $list
     */
    public function __construct($list = [])
    {
        $this->list = $list;
    }

    /**
     * @inheritDoc
     */
    public function getAdditionalLocations() : array
    {
        return $this->getLocations();
    }

    /**
     * @return array
     */
    public function getLocations() : array
    {
        return $this->list;
    }
}
