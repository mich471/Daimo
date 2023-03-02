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

namespace Plumrocket\CookieConsent\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Plumrocket\CookieConsent\Model\Cookie\ClearRejected;
use Psr\Log\LoggerInterface;

/**
 * @since 1.0.0
 */
class ClearRejectedCookie implements ObserverInterface
{
    /**
     * @var \Plumrocket\CookieConsent\Model\Cookie\ClearRejected
     */
    private $clearRejected;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Plumrocket\CookieConsent\Model\Cookie\ClearRejected $clearRejected
     * @param \Psr\Log\LoggerInterface                             $logger
     */
    public function __construct(
        ClearRejected $clearRejected,
        LoggerInterface $logger
    ) {
        $this->clearRejected = $clearRejected;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        try {
            $this->clearRejected->execute();
        } catch (InputException $e) {
            $this->logger->error($e->getMessage());
        } catch (FailureToSendException $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
