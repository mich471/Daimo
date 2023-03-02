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
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Base\Helper;

class Main extends \Plumrocket\Base\Helper\Base
{
    final public function getCustomerKey()
    {
        $customerKey = '054618e1f3c9cff69e0c7780fa3a19cec1f5a53080';

        if (method_exists($this, 'getTrueCustomerKey')) {
            return $this->getTrueCustomerKey($customerKey);
        }

        return $customerKey;
    }
}
