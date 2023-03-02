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
interface PolicyInterface
{

    public const ID = 'id';
    public const TITLE = 'title';
    public const URL_KEY = 'url_key';
    public const CONTENT = 'content';
    public const VERSION = 'version';

    /**
     * @return string|int|null
     */
    public function getId();

    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @return string
     */
    public function getUrlKey(): string;

    /**
     * @return string
     */
    public function getVersion(): string;

    /**
     * @return string
     */
    public function getContent(): string;

    /**
     * @param $id
     * @return mixed
     */
    public function setId($id);

    /**
     * @param string $title
     * @return \Plumrocket\DataPrivacyApi\Api\Data\PolicyInterface
     */
    public function setTitle(string $title): PolicyInterface;

    /**
     * @param string $urlKey
     * @return \Plumrocket\DataPrivacyApi\Api\Data\PolicyInterface
     */
    public function setUrlKey(string $urlKey): PolicyInterface;

    /**
     * @param string $version
     * @return \Plumrocket\DataPrivacyApi\Api\Data\PolicyInterface
     */
    public function setVersion(string $version): PolicyInterface;

    /**
     * @param string $content
     * @return \Plumrocket\DataPrivacyApi\Api\Data\PolicyInterface
     */
    public function setContent(string $content): PolicyInterface;
}
