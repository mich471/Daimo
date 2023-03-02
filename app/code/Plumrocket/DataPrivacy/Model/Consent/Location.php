<?php
/**
 * @package     Plumrocket_magento_2_3_6__1
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model\Consent;

use Plumrocket\DataPrivacyApi\Api\Data\ConsentLocationInterface as DataConsentLocation;

/**
 * @method $this setData($key, $value = null)
 */
class Location extends \Magento\Framework\Model\AbstractModel implements DataConsentLocation
{

    /**
     * @var \Plumrocket\GDPR\Api\ConsentLocationTypeInterface
     */
    private $consentLocationType;

    /**
     * Location constructor.
     *
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Plumrocket\DataPrivacyApi\Api\ConsentLocationTypeInterface  $consentLocationType
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Plumrocket\DataPrivacyApi\Api\ConsentLocationTypeInterface $consentLocationType,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->consentLocationType = $consentLocationType;
    }

    /**
     * Initialize resources
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Plumrocket\DataPrivacy\Model\ResourceModel\Consent\Location::class);
    }

    /**
     * @inheritDoc
     */
    public function isSystem(): bool
    {
        return $this->consentLocationType->isSystem($this->getType());
    }

    /**
     * @inheritDoc
     */
    public function getType(): int
    {
        return (int) $this->getData(DataConsentLocation::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setType(int $type): DataConsentLocation
    {
        return $this->setData(DataConsentLocation::TYPE, $type);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return (string) $this->getData(DataConsentLocation::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): DataConsentLocation
    {
        return $this->setData(DataConsentLocation::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getLocationKey(): string
    {
        return (string) $this->getData(DataConsentLocation::LOCATION_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setLocationKey(string $locationKey): DataConsentLocation
    {
        return $this->setData(DataConsentLocation::LOCATION_KEY, $locationKey);
    }

    /**
     * @inheritDoc
     */
    public function isVisible(): bool
    {
        return $this->getVisibility();
    }

    /**
     * @inheritDoc
     */
    public function getVisibility(): bool
    {
        return (bool) $this->getData(DataConsentLocation::VISIBLE);
    }

    /**
     * @inheritDoc
     */
    public function setVisibility(bool $flag): DataConsentLocation
    {
        return $this->setData(DataConsentLocation::VISIBLE, $flag);
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return (string) $this->getData(DataConsentLocation::DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setDescription(string $description): DataConsentLocation
    {
        return $this->setData(DataConsentLocation::DESCRIPTION, $description);
    }
}
