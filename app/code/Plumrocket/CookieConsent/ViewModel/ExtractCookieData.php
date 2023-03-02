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

namespace Plumrocket\CookieConsent\ViewModel;

use Plumrocket\CookieConsent\Api\Data\CookieInterface;
use Plumrocket\CookieConsent\Model\Cookie\Attribute\Source\Type;
use Plumrocket\CookieConsent\Model\Cookie\Domain;
use Plumrocket\CookieConsent\Model\Cookie\Duration;

/**
 * @since 1.0.0
 */
class ExtractCookieData
{
    /**
     * @var \Plumrocket\CookieConsent\Model\Cookie\Domain
     */
    private $cookieDomain;

    /**
     * @var \Plumrocket\CookieConsent\Model\Cookie\Duration
     */
    private $cookieDuration;

    /**
     * @var \Plumrocket\CookieConsent\Model\Cookie\Attribute\Source\Type
     */
    private $type;

    /**
     * @param \Plumrocket\CookieConsent\Model\Cookie\Domain                $cookieDomain
     * @param \Plumrocket\CookieConsent\Model\Cookie\Duration              $cookieDuration
     * @param \Plumrocket\CookieConsent\Model\Cookie\Attribute\Source\Type $type
     */
    public function __construct(
        Domain $cookieDomain,
        Duration $cookieDuration,
        Type $type
    ) {
        $this->cookieDomain = $cookieDomain;
        $this->cookieDuration = $cookieDuration;
        $this->type = $type;
    }

    /**
     * @param \Plumrocket\CookieConsent\Api\Data\CookieInterface $cookie
     * @return array
     */
    public function execute(CookieInterface $cookie): array
    {
        return [
            CookieInterface::NAME => $cookie->getName(),
            CookieInterface::CATEGORY_KEY => $cookie->getCategoryKey(),
            CookieInterface::TYPE => $cookie->getType(),
            'typeLabel' => $this->type->getOptionText($cookie->getType()),
            CookieInterface::DOMAIN => $cookie->getDomain(),
            'domainLabel' => $this->cookieDomain->getLabel($cookie),
            CookieInterface::DURATION => $cookie->getDuration(),
            'durationLabel' => $this->cookieDuration->getLabel($cookie),
            CookieInterface::DESCRIPTION => $cookie->getDescription(),
        ];
    }
}
