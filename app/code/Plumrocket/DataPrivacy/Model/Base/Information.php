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

namespace Plumrocket\DataPrivacy\Model\Base;

/**
 * @deprecated since 3.2.0
 */
class Information extends \Plumrocket\Base\Model\Extensions\Information
{
    const IS_SERVICE = false;
    const NAME = 'Data Privacy';
    const WIKI = '';
    const CONFIG_SECTION = 'prgdpr';
    const IS_ENABLED_FIELD_CONFIG_PATH = 'prgdpr/general/enabled';
    const MODULE_NAME = 'DataPrivacy';
}
