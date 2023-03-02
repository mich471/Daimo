<?php
/**
 * @package     Plumrocket_DataPrivacyApi
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\DataPrivacyApi\Api\Data;

/**
 * @since 2.0.0
 */
interface ConsentLocationInterface
{
    const LOCATION_KEY = 'location_key';
    const TYPE = 'type';
    const VISIBLE = 'visible';
    const NAME = 'name';
    const DESCRIPTION = 'description';

    /**
     * @return string|int
     */
    public function getId();

    /**
     * @return bool
     */
    public function isSystem() : bool;

    /**
     * @return bool
     */
    public function isVisible() : bool;

    /**
     * @return string
     */
    public function getLocationKey() : string;

    /**
     * @param string $locationKey
     * @return \Plumrocket\DataPrivacyApi\Api\Data\ConsentLocationInterface
     */
    public function setLocationKey(string $locationKey) : self;

    /**
     * @param int $type
     * @return ConsentLocationInterface
     */
    public function setType(int $type) : self;

    /**
     * @return int
     */
    public function getType() : int;

    /**
     * @param bool $flag
     * @return ConsentLocationInterface
     */
    public function setVisibility(bool $flag) : self;

    /**
     * @return bool
     */
    public function getVisibility() : bool;

    /**
     * @return string
     */
    public function getName() : string;

    /**
     * @param string $name
     * @return ConsentLocationInterface
     */
    public function setName(string $name) : self;

    /**
     * @return string
     */
    public function getDescription() : string;

    /**
     * @param string $description
     * @return ConsentLocationInterface
     */
    public function setDescription(string $description) : self;
}
