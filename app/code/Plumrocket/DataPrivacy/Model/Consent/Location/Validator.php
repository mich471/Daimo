<?php
/**
 * @package     Plumrocket_magento_2_3_6__1
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model\Consent\Location;

use Plumrocket\DataPrivacy\Model\ResourceModel\Consent\Location as LocationResource;

class Validator
{
    /**
     * @var string[]|null
     */
    private $locationKeys;

    /**
     * @var \Plumrocket\DataPrivacy\Model\ResourceModel\Consent\Location
     */
    private $locationResource;

    /**
     * @param \Plumrocket\DataPrivacy\Model\ResourceModel\Consent\Location $locationResource
     */
    public function __construct(LocationResource $locationResource)
    {
        $this->locationResource = $locationResource;
    }

    /**
     * @param string $locationKey
     * @return bool
     */
    public function isValid(string $locationKey): bool
    {
        if (null === $this->locationKeys) {
            $this->locationKeys = $this->locationResource->getAllLocationKeys();
        }

        return in_array($locationKey, $this->locationKeys, true);
    }
}
