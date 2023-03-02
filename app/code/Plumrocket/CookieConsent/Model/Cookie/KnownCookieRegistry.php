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

use Magento\Framework\Config\DataInterface;
use Magento\Framework\Session\Config\ConfigInterface;
use Magento\Persistent\Helper\Data as PersistentHelper;
use Plumrocket\CookieConsent\Api\GetUserConsentInterface;
use Plumrocket\CookieConsent\Api\KnownCookieRegistryInterface;
use Plumrocket\CookieConsent\Helper\Config;
use Plumrocket\CookieConsent\Model\Category\Attribute\Source\CategoryKey;
use Plumrocket\CookieConsent\Model\Cookie\Attribute\Source\Type;

/**
 * @since 1.0.0
 */
class KnownCookieRegistry implements KnownCookieRegistryInterface
{
    /**
     * @var array
     */
    private $list;

    /**
     * @var \Magento\Framework\Session\Config\ConfigInterface
     */
    private $sessionConfig;

    /**
     * @var \Plumrocket\CookieConsent\Helper\Config
     */
    private $config;

    /**
     * @var \Magento\Persistent\Helper\Data
     */
    private $persistentHelper;

    /**
     * @var \Magento\Framework\Config\DataInterface
     */
    private $cookieConfig;

    /**
     * @param \Magento\Framework\Session\Config\ConfigInterface $sessionConfig
     * @param \Plumrocket\CookieConsent\Helper\Config           $config
     * @param \Magento\Persistent\Helper\Data                   $persistentHelper
     * @param \Magento\Framework\Config\DataInterface           $cookieConfig
     * @param array                                             $list
     */
    public function __construct(
        ConfigInterface $sessionConfig,
        Config $config,
        PersistentHelper $persistentHelper,
        DataInterface $cookieConfig,
        array $list = []
    ) {
        $this->list = $list;
        $this->sessionConfig = $sessionConfig;
        $this->config = $config;
        $this->persistentHelper = $persistentHelper;
        $this->cookieConfig = $cookieConfig;
    }

    /**
     * @inheritDoc
     */
    public function getList(): array
    {
        $magentoCookieLifeTime = $this->sessionConfig->getCookieLifetime();
        $persistentLifeTime = $this->persistentHelper->getLifeTime();

        $dynamicCookie = [
            GetUserConsentInterface::COOKIE_CONSENT_NAME => [
                'category_key' => CategoryKey::KEY_NECESSARY,
                'type' => Type::TYPE_FIRST,
                'duration' => $this->config->getConsentExpiry(),
                'description' => 'Keeps your cookie consent.'
            ],
            'form_key' => [
                'category_key' => CategoryKey::KEY_NECESSARY,
                'type' => Type::TYPE_FIRST,
                'duration' => $magentoCookieLifeTime,
                'description' => 'A security measure that appends a random ' .
                    'string to all form submissions to protect the data from ' .
                    'Cross-Site Request Forgery (CSRF).'
            ],
            'X-Magento-Vary' => [
                'category_key' => CategoryKey::KEY_NECESSARY,
                'type' => Type::TYPE_FIRST,
                'duration' => $magentoCookieLifeTime,
                'description' => 'Configuration setting that improves performance ' .
                    'when using Varnish static content caching.'
            ],
            'private_content_version' => [
                'category_key' => CategoryKey::KEY_NECESSARY,
                'type' => Type::TYPE_FIRST,
                'duration' => $persistentLifeTime,
                'description' => 'Appends a random, unique number and time to pages with customer content to prevent ' .
                    'them from being cached on the server.'
            ],

            'persistent_shopping_cart' => [
                'category_key' => CategoryKey::KEY_NECESSARY,
                'type' => Type::TYPE_FIRST,
                'duration' => $persistentLifeTime,
                'description' => 'Stores the key (ID) of persistent cart to make it possible to restore the cart ' .
                    'for an anonymous shopper.'
            ],

            // Magento Commerce only

            'add_to_cart' => [
                'category_key' => CategoryKey::KEY_STATISTICS,
                'type' => Type::TYPE_FIRST,
                'duration' => Duration::ONE_HOUR,
                'description' => 'Used by Google Tag Manager. Captures the product SKU, name, price and quantity ' .
                    'removed from the cart, and makes the information available for future integration by third-party '.
                    'scripts.'
            ],

            'mage-banners-cache-storage' => [
                'category_key' => CategoryKey::KEY_NECESSARY,
                'type' => Type::TYPE_FIRST,
                'duration' => Duration::ONE_HOUR,
                'description' => 'Stores banner content locally to improve performance.'
            ],

            'remove_from_cart' => [
                'category_key' => CategoryKey::KEY_STATISTICS,
                'type' => Type::TYPE_FIRST,
                'duration' => Duration::ONE_HOUR,
                'description' => 'Used by Google Tag Manager. Captures the product SKU, name, price and quantity ' .
                    'added to the cart, and makes the information available for future integration by third-party ' .
                    'scripts.'
            ],
        ];

        return array_merge($this->cookieConfig->get('cookies'), $this->list, $dynamicCookie);
    }
}
