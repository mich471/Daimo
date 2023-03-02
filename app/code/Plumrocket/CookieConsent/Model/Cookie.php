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

namespace Plumrocket\CookieConsent\Model;

use Magento\Catalog\Model\AbstractModel as CatalogAbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Plumrocket\CookieConsent\Api\Data\CookieInterface;
use Plumrocket\CookieConsent\Model\Cookie\Attribute\Source\Type as CookieType;

/**
 * @since 1.0.0
 */
class Cookie extends CatalogAbstractModel implements IdentityInterface, CookieInterface
{
    const ENTITY = 'pr_cookie';

    const CACHE_TAG = 'pr_cookie';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'pr_cookie';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'cookie';

    /**
     * Model cache tag for clear cache in after save and after delete
     *
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Initialize resources
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Cookie::class);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return (int) parent::getId();
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return (string) $this->_getData(CookieInterface::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): CookieInterface
    {
        return $this->setData(CookieInterface::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getCategoryKey(): string
    {
        return (string) $this->_getData(CookieInterface::CATEGORY_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setCategoryKey(string $categoryKey): CookieInterface
    {
        return $this->setData(CookieInterface::CATEGORY_KEY, $categoryKey);
    }

    /**
     * @inheritDoc
     */
    public function isFirstParty(): bool
    {
        return CookieType::TYPE_FIRST === $this->getType();
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return (string) $this->_getData(CookieInterface::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setType(string $type): CookieInterface
    {
        return $this->setData(CookieInterface::TYPE, $type);
    }

    /**
     * @inheritDoc
     */
    public function getDomain(): string
    {
        return (string) $this->_getData(CookieInterface::DOMAIN);
    }

    /**
     * @inheritDoc
     */
    public function setDomain(string $domain): CookieInterface
    {
        return $this->setData(CookieInterface::DOMAIN, $domain);
    }

    /**
     * @inheritDoc
     */
    public function getDuration(): int
    {
        return (int) $this->_getData(CookieInterface::DURATION);
    }

    /**
     * @inheritDoc
     */
    public function setDuration(int $duration): CookieInterface
    {
        return $this->setData(CookieInterface::DURATION, $duration);
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return (string) $this->_getData(CookieInterface::DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setDescription(string $description): CookieInterface
    {
        return $this->setData(CookieInterface::DESCRIPTION, $description);
    }
}
