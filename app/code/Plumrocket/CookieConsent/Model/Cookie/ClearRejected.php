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

namespace Plumrocket\CookieConsent\Model\Cookie;

use Magento\Framework\Session\Config\ConfigInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;

/**
 * @since 1.0.0
 */
class ClearRejected
{
    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var \Plumrocket\CookieConsent\Model\Cookie\IsAllowed
     */
    private $isAllowed;

    /**
     * @var \Plumrocket\CookieConsent\Model\Cookie\GetSetCookiesNames
     */
    private $getSetCookiesNames;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var \Magento\Framework\Session\Config\ConfigInterface
     */
    private $sessionConfig;

    /**
     * @param \Magento\Framework\Stdlib\CookieManagerInterface          $cookieManager
     * @param \Plumrocket\CookieConsent\Model\Cookie\IsAllowed          $isAllowed
     * @param \Plumrocket\CookieConsent\Model\Cookie\GetSetCookiesNames $getSetCookiesNames
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory    $cookieMetadataFactory
     * @param \Magento\Framework\Session\Config\ConfigInterface         $sessionConfig
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        IsAllowed $isAllowed,
        GetSetCookiesNames $getSetCookiesNames,
        CookieMetadataFactory $cookieMetadataFactory,
        ConfigInterface $sessionConfig
    ) {
        $this->cookieManager = $cookieManager;
        $this->isAllowed = $isAllowed;
        $this->getSetCookiesNames = $getSetCookiesNames;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionConfig = $sessionConfig;
    }

    /**
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function execute()
    {
        $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
        $metadata->setPath('/');
        $metadata->setSecure($this->sessionConfig->getCookieSecure());
        $metadata->setHttpOnly($this->sessionConfig->getCookieHttpOnly());

        foreach ($this->getSetCookiesNames->execute() as $cookieName) {
            if (! $this->isAllowed->execute($cookieName)) {
                $this->cookieManager->deleteCookie($cookieName, $metadata);
            }
        }
    }
}
