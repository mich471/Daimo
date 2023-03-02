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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Api;

/**
 * You can pass additional locations via DI
 * either by pass into constructor or using After plugin on getLocations method
 *
 * They'll installed during recurring step of setup:upgrade
 *
 * @deprecated since 3.1.0
 * @see etc/pr_data_privacy_consent_location.xml
 */
interface ConsentLocationRegistryInterface
{
    /**
     * @deprecated since 2.0.0
     * @see \Plumrocket\GDPR\Api\ConsentLocationRegistryInterface::getLocations
     *
     * @return array
     */
    public function getAdditionalLocations() : array;

    /**
     * @return array[]
     * @since 2.0.0
     */
    public function getLocations() : array;
}
