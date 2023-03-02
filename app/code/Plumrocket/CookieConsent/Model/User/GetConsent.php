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

namespace Plumrocket\CookieConsent\Model\User;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\Cookie\CookieReaderInterface;
use Plumrocket\CookieConsent\Api\Data\CategoryInterface;
use Plumrocket\CookieConsent\Api\GetUserConsentInterface;

/**
 * @since 1.0.0
 */
class GetConsent implements GetUserConsentInterface
{
    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieReaderInterface
     */
    private $cookieReader;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @param \Magento\Framework\Stdlib\Cookie\CookieReaderInterface $cookieReader
     * @param \Magento\Framework\Serialize\SerializerInterface       $serializer
     */
    public function __construct(
        CookieReaderInterface $cookieReader,
        SerializerInterface $serializer
    ) {
        $this->cookieReader = $cookieReader;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function execute(): array
    {
        $consent = $this->cookieReader->getCookie(self::COOKIE_CONSENT_NAME);
        if (null === $consent) {
            throw new NotFoundException(__('User didnt make any consents.'));
        }

        /**
         * todo: remove in 2022
         *
         * Previously we used "*" for "all categories" but cookie with this value is blocked by mod security
         * @see REQUEST-942-APPLICATION-ATTACK-SQLI
         */
        $consent = str_replace('*', CategoryInterface::ALL_CATEGORIES, $consent);
        return $this->serializer->unserialize($consent);
    }
}
