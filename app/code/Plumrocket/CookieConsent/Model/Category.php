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
use Plumrocket\CookieConsent\Api\Data\CategoryInterface;

/**
 * @since 1.0.0
 * @method $this setData($key, $value = null)
 */
class Category extends CatalogAbstractModel implements IdentityInterface, CategoryInterface
{
    const ENTITY = 'pr_cookie_category';

    const CACHE_TAG = 'pr_cookie_category';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'pr_cookie_category';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'category';

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
        $this->_init(ResourceModel\Category::class);
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
    public function isEnabled(): bool
    {
        return (bool) $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus(bool $status): CategoryInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function isPreChecked(): bool
    {
        return (bool) $this->getData(self::IS_PRE_CHECKED);
    }

    /**
     * @inheritDoc
     */
    public function setIsPreChecked(bool $flag): CategoryInterface
    {
        return $this->setData(self::IS_PRE_CHECKED, $flag);
    }

    /**
     * @inheritDoc
     */
    public function isEssential(): bool
    {
        return (bool) $this->getData(self::IS_ESSENTIAL);
    }

    /**
     * @inheritDoc
     */
    public function setIsEssential(bool $isEssential): CategoryInterface
    {
        return $this->setData(self::IS_ESSENTIAL, $isEssential);
    }

    /**
     * @inheritDoc
     */
    public function getKey(): string
    {
        return (string) $this->getData(self::KEY);
    }

    /**
     * @inheritDoc
     */
    public function setKey(string $key): CategoryInterface
    {
        return $this->setData(self::KEY, $key);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return (string) $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): CategoryInterface
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return (string) $this->getData(self::DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setDescription(string $description): CategoryInterface
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder(): int
    {
        return (int) $this->getData(self::SORT_ORDER);
    }

    /**
     * @inheritDoc
     */
    public function setSortOrder(int $sortOrder): CategoryInterface
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * @inheritDoc
     */
    public function getHeadScripts(): string
    {
        return (string) $this->getData(self::HEAD_SCRIPTS);
    }

    /**
     * @inheritDoc
     */
    public function setHeadScripts(string $headScripts): CategoryInterface
    {
        return $this->setData(self::HEAD_SCRIPTS, $headScripts);
    }

    /**
     * @inheritDoc
     */
    public function getFooterMiscellaneousHtml(): string
    {
        return (string) $this->getData(self::FOOTER_MISCELLANEOUS_HTML);
    }

    /**
     * @inheritDoc
     */
    public function setFooterMiscellaneousHtml(string $footerMiscellaneousHtml): CategoryInterface
    {
        return $this->setData(self::FOOTER_MISCELLANEOUS_HTML, $footerMiscellaneousHtml);
    }
}
