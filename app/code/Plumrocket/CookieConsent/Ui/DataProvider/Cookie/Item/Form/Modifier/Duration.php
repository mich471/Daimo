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

namespace Plumrocket\CookieConsent\Ui\DataProvider\Cookie\Item\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Plumrocket\CookieConsent\Model\Cookie\Attribute\DurationDecorator;
use Plumrocket\CookieConsent\Ui\Locator\LocatorInterface;

/**
 * @since 1.0.0
 */
class Duration implements ModifierInterface
{
    /**
     * @var \Plumrocket\CookieConsent\Ui\Locator\LocatorInterface
     */
    private $locator;

    /**
     * @var \Plumrocket\CookieConsent\Model\Cookie\Attribute\DurationDecorator
     */
    private $durationDecorator;

    /**
     * @param \Plumrocket\CookieConsent\Ui\Locator\LocatorInterface              $locator
     * @param \Plumrocket\CookieConsent\Model\Cookie\Attribute\DurationDecorator $durationDecorator
     */
    public function __construct(LocatorInterface $locator, DurationDecorator $durationDecorator)
    {
        $this->locator = $locator;
        $this->durationDecorator = $durationDecorator;
    }

    public function modifyData(array $data)
    {
        if ($cookieId = $this->locator->getModel()->getId()) {
            $data[$cookieId] = $this->durationDecorator->unserializeParams($data[$cookieId]);
        } else {
            $data[$cookieId] = $this->durationDecorator->unserializeParams([]);
        }

        return $data;
    }

    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
