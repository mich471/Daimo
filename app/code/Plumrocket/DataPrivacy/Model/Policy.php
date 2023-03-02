<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model;

use Magento\Framework\DataObject;
use Plumrocket\DataPrivacyApi\Api\Data\PolicyInterface;

class Policy extends DataObject implements PolicyInterface
{

    public function getId()
    {
        return $this->_getData(self::ID);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return (string) $this->_getData(self::TITLE);
    }

    /**
     * @inheritDoc
     */
    public function getUrlKey(): string
    {
        return (string) $this->_getData(self::URL_KEY);
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return (string) $this->_getData(self::VERSION);
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return (string) $this->_getData(self::CONTENT);
    }

    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function setTitle(string $title): PolicyInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @inheritDoc
     */
    public function setUrlKey(string $urlKey): PolicyInterface
    {
        return $this->setData(self::URL_KEY, $urlKey);
    }

    /**
     * @inheritDoc
     */
    public function setVersion(string $version): PolicyInterface
    {
        return $this->setData(self::VERSION, $version);
    }

    /**
     * @inheritDoc
     */
    public function setContent(string $content): PolicyInterface
    {
        return $this->setData(self::CONTENT, $content);
    }
}
