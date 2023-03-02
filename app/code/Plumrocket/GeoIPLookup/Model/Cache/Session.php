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
 * @package     Plumrocket_Csp
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\GeoIPLookup\Model\Cache;

use Magento\Framework\Session\SessionManagerInterface;

/**
 * @since 1.2.2
 */
class Session implements GeoIpInterface
{
    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @param \Magento\Framework\Session\SessionManagerInterface $sessionManager
     */
    public function __construct(SessionManagerInterface $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    /**
     * @return mixed
     */
    public function get()
    {
        $sessionData = $this->sessionManager->getPrGeoipData();
        $result = [];

        if (!empty($sessionData) && is_array($sessionData)) {
            $result = $sessionData;
        }

        return $result;
    }

    /**
     * @param $data
     */
    public function set($data)
    {
        $this->sessionManager->setPrGeoipData($data);
    }
}
