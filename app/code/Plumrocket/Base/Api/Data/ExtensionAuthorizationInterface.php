<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Api\Data;

/**
 * @since 2.5.0
 */
interface ExtensionAuthorizationInterface
{
    const SIGNATURE = 'signature';
    const STATUS = 'status';
    const DATE = 'date';

    /**
     * @return string
     */
    public function getModuleName(): string;

    /**
     * Check if product is in stock
     *
     * @return bool
     */
    public function isAuthorized(): bool;

    /**
     * @return int
     */
    public function getStatus(): int;

    /**
     * @param int $status
     * @return \Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface
     */
    public function setStatus(int $status): ExtensionAuthorizationInterface;

    /**
     * @param string $signature
     * @return \Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface
     */
    public function setSignature(string $signature): ExtensionAuthorizationInterface;

    /**
     * @return string
     */
    public function getDate(): string;

    /**
     * @param string $date
     * @return \Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface
     */
    public function setDate(string $date): ExtensionAuthorizationInterface;

    /**
     * @return bool
     */
    public function isCached(): bool;
}
