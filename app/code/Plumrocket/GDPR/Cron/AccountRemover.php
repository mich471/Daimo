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
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Cron;

use Plumrocket\DataPrivacy\Cron\RemoveCustomersByRequests;

/**
 * Scheduler to clean accounts marked to be deleted or anonymized.
 *
 * @deprecated since 3.1.0
 * @see \Plumrocket\DataPrivacy\Cron\RemoveCustomersByRequests
 */
class AccountRemover extends RemoveCustomersByRequests
{
}
