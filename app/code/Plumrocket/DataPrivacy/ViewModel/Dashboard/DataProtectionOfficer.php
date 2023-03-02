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
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\ViewModel\Dashboard;

use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Some of Data Protection addons have plugins for this class
 *
 * @since 3.0.1
 */
class DataProtectionOfficer implements ArgumentInterface
{
    /**
     * Can be changed by plugin
     *
     * @return string
     */
    public function getEmail(): string
    {
        return '';
    }

    /**
     * Can be changed by plugin
     *
     * @return bool
     */
    public function canShow(): bool
    {
        return false;
    }
}
