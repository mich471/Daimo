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

namespace Plumrocket\CookieConsent\Model\Consent;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Plumrocket\CookieConsent\Api\Data\ConsentLogInterface;
use Plumrocket\CookieConsent\Api\Data\ConsentLogInterfaceFactory;
use Plumrocket\CookieConsent\Model\ResourceModel\ConsentLog as ConsentLogResource;

/**
 * @since 1.0.0
 */
class Logger
{
    /**
     * @var \Plumrocket\CookieConsent\Api\Data\ConsentLogInterfaceFactory
     */
    private $consentLogFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var \Plumrocket\CookieConsent\Model\ResourceModel\ConsentLog
     */
    private $consentLogResource;

    /**
     * @param \Plumrocket\CookieConsent\Api\Data\ConsentLogInterfaceFactory $consentLogFactory
     * @param \Magento\Store\Model\StoreManagerInterface                    $storeManager
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress          $remoteAddress
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                   $dateTime
     * @param \Magento\Customer\Helper\Session\CurrentCustomer              $currentCustomer
     * @param \Plumrocket\CookieConsent\Model\ResourceModel\ConsentLog      $consentLogResource
     */
    public function __construct(
        ConsentLogInterfaceFactory $consentLogFactory,
        StoreManagerInterface $storeManager,
        RemoteAddress $remoteAddress,
        DateTime $dateTime,
        CurrentCustomer $currentCustomer,
        ConsentLogResource $consentLogResource
    ) {
        $this->consentLogFactory = $consentLogFactory;
        $this->storeManager = $storeManager;
        $this->remoteAddress = $remoteAddress;
        $this->dateTime = $dateTime;
        $this->currentCustomer = $currentCustomer;
        $this->consentLogResource = $consentLogResource;
    }

    /**
     * @param array $settings
     * @return \Plumrocket\CookieConsent\Api\Data\ConsentLogInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function log(array $settings): ConsentLogInterface
    {
        /** @var ConsentLogInterface|Log $consentLog */
        $consentLog = $this->consentLogFactory->create();

        $consentLog->setCustomerId((int) $this->currentCustomer->getCustomerId());
        $consentLog->setSettings($settings);
        $consentLog->setWebsiteId((int) $this->storeManager->getStore()->getWebsiteId());
        $consentLog->setIpAddress($this->remoteAddress->getRemoteAddress());
        $consentLog->setCreatedAt($this->getFormattedGmtDateTime());

        $this->consentLogResource->save($consentLog);

        return $consentLog;
    }

    /**
     * @return string
     */
    private function getFormattedGmtDateTime(): string
    {
        return (string) date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp());
    }
}
