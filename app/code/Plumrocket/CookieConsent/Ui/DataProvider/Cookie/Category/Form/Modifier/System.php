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

namespace Plumrocket\CookieConsent\Ui\DataProvider\Cookie\Category\Form\Modifier;

use Plumrocket\CookieConsent\Ui\DataProvider\AbstractForm\SystemModifier;

/**
 * @since 1.0.0
 */
class System extends SystemModifier
{
    /**
     * @var array
     */
    protected $actionUrls = [
        self::KEY_SUBMIT_URL => 'pr_cookie/category/save',
    ];
}
