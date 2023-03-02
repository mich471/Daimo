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

namespace Plumrocket\CookieConsent\Plugin;

use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Framework\Stdlib\Cookie\SensitiveCookieMetadata;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Plumrocket\CookieConsent\Api\IsAllowedCookieInterface;

/**
 * @since 1.0.0
 */
class CookieManagerPlugin
{
    /**
     * @var \Plumrocket\CookieConsent\Api\IsAllowedCookieInterface
     */
    private $isAllowCookie;

    /**
     * @param \Plumrocket\CookieConsent\Api\IsAllowedCookieInterface $isAllowCookie
     */
    public function __construct(IsAllowedCookieInterface $isAllowCookie)
    {
        $this->isAllowCookie = $isAllowCookie;
    }

    /**
     * @param \Magento\Framework\Stdlib\CookieManagerInterface           $subject
     * @param callable                                                   $proceed
     * @param                                                            $name
     * @param                                                            $value
     * @param \Magento\Framework\Stdlib\Cookie\PublicCookieMetadata|null $metadata
     */
    public function aroundSetPublicCookie(
        CookieManagerInterface $subject,
        callable $proceed,
        $name,
        $value,
        PublicCookieMetadata $metadata = null
    ) {
        if ($this->isAllowCookie->execute($name)) {
            $proceed($name, $value, $metadata);
        }
    }

    /**
     * @param \Magento\Framework\Stdlib\CookieManagerInterface              $subject
     * @param callable                                                      $proceed
     * @param                                                               $name
     * @param                                                               $value
     * @param \Magento\Framework\Stdlib\Cookie\SensitiveCookieMetadata|null $metadata
     */
    public function aroundSetSensitiveCookie(
        CookieManagerInterface $subject,
        callable $proceed,
        $name,
        $value,
        SensitiveCookieMetadata $metadata = null
    ) {
        if ($this->isAllowCookie->execute($name)) {
            $proceed($name, $value, $metadata);
        }
    }
}
