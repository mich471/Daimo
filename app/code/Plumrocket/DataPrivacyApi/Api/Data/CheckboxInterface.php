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
interface CheckboxInterface
{
    public const STORE_ID = 'store_id';
    public const STATUS = 'status';
    public const LOCATION_KEY = 'location_key';
    public const LABEL = 'label';
    public const CMS_PAGE_ID = 'cms_page_id';
    public const REQUIRE = 'require';
    public const GEO_TARGETING = 'geo_targeting';
    public const GEO_TARGETING_USA_STATES = 'geo_targeting_usa_states';
    public const INTERNAL_NOTE = 'internal_note';

    /**
     * @return string|int
     */
    public function getId();

    /**
     * @return bool
     */
    public function isRequiredForValidate(): bool;

    /**
     * @return bool
     */
    public function getStatus(): bool;

    /**
     * @param bool $status
     * @return \Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface
     */
    public function setStatus(bool $status): CheckboxInterface;

    /**
     * @return array
     */
    public function getLocationKeys(): array;

    /**
     * @param array $locationKeys
     * @return \Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface
     */
    public function setLocationKeys(array $locationKeys): CheckboxInterface;

    /**
     * @param string $locationKey
     * @return bool
     */
    public function isUsedInLocation(string $locationKey): bool;

    /**
     * @param bool $formatLabel
     * @return string
     */
    public function getLabel(bool $formatLabel = true): string;

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel(string $label): CheckboxInterface;

    /**
     * @return bool
     */
    public function getRequire(): bool;

    /**
     * @param bool $isRequired
     * @return $this
     */
    public function setRequire(bool $isRequired): CheckboxInterface;

    /**
     * @return array
     */
    public function getGeoTargeting(): array;

    /**
     * @return array
     */
    public function getGeoTargetingUsaStates(): array;

    /**
     * @param array $geoTargeting
     * @return $this
     */
    public function setGeoTargeting(array $geoTargeting): CheckboxInterface;

    /**
     * @return string
     */
    public function getInternalNote(): string;

    /**
     * @param string $internalNote
     * @return $this
     */
    public function setInternalNote(string $internalNote): CheckboxInterface;

    /**
     * Retrieve Store Id
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Set checkbox store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId): CheckboxInterface;

    /**
     * @return \Plumrocket\DataPrivacyApi\Api\Data\PolicyInterface|null
     */
    public function getPolicy(): ?PolicyInterface;
}
