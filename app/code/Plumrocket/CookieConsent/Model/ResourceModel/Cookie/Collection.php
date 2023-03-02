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

namespace Plumrocket\CookieConsent\Model\ResourceModel\Cookie;

use Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection;
use Plumrocket\CookieConsent\Api\Data\CookieInterface;
use Plumrocket\CookieConsent\Model\Cookie;
use Plumrocket\CookieConsent\Model\ResourceModel\Cookie as CookieResource;

/**
 * @since 1.0.0
 * @method CookieInterface[]|Cookie[] getItems()
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $idFieldName = CookieResource::ID_FIELD_NAME;

    /**
     * Resource initialization.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Cookie::class, CookieResource::class);
    }
}
