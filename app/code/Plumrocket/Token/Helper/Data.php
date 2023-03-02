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
 * @package     Plumrocket_Token
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Token\Helper;

/**
 * @since 1.0.0
 * @deprecated since 1.0.5
 * TODO: remove in next release
 */
class Data extends Main
{
    /**
     * Section name for configs
     */
    const SECTION_ID = 'pr_token';

    /**
     * @var string
     */
    protected $_configSectionId = self::SECTION_ID;

    /**
     * @param null $store
     * @return bool
     */
    public function moduleEnabled($store = null)
    {
        return true;
    }
}
