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

namespace Plumrocket\CookieConsent\Controller\Adminhtml\Item;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\StoreManagerInterface;
use Plumrocket\CookieConsent\Api\CookieRepositoryInterface;
use Plumrocket\CookieConsent\Model\Cookie;
use Plumrocket\CookieConsent\Model\CookieFactory;
use Plumrocket\CookieConsent\Ui\Locator\CookieLocatorInterface;

/**
 * @since 1.0.0
 */
class Builder
{
    /**
     * @var \Plumrocket\CookieConsent\Model\CookieFactory
     */
    private $cookieFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Store\Model\StoreFactory
     */
    private $storeFactory;

    /**
     * @var \Plumrocket\CookieConsent\Ui\Locator\CookieLocatorInterface
     */
    private $cookieLocator;

    /**
     * @var \Plumrocket\CookieConsent\Api\CookieRepositoryInterface
     */
    private $cookieRepository;

    /**
     * @param \Plumrocket\CookieConsent\Model\CookieFactory               $cookieFactory
     * @param \Magento\Store\Model\StoreManagerInterface                  $storeManager
     * @param \Magento\Store\Model\StoreFactory                           $storeFactory
     * @param \Plumrocket\CookieConsent\Ui\Locator\CookieLocatorInterface $cookieLocator
     * @param \Plumrocket\CookieConsent\Api\CookieRepositoryInterface     $cookieRepository
     */
    public function __construct(
        CookieFactory $cookieFactory,
        StoreManagerInterface $storeManager,
        StoreFactory $storeFactory,
        CookieLocatorInterface $cookieLocator,
        CookieRepositoryInterface $cookieRepository
    ) {
        $this->cookieFactory = $cookieFactory;
        $this->storeManager = $storeManager;
        $this->storeFactory = $storeFactory;
        $this->cookieLocator = $cookieLocator;
        $this->cookieRepository = $cookieRepository;
    }

    /**
     * Build cookie item based on user request
     *
     * @param RequestInterface $request
     * @return \Plumrocket\CookieConsent\Model\Cookie
     */
    public function build(RequestInterface $request): Cookie
    {
        $storeId = (int) $request->getParam('store', 0);
        $cookieId = (int) $request->getParam('id');

        $store = $this->storeManager->getStore($storeId);
        $this->storeManager->setCurrentStore($store->getCode());

        if ($cookieId) {
            try {
                $cookie = $this->cookieRepository->getById($cookieId, $storeId);
            } catch (NoSuchEntityException $e) {
                /** @var \Plumrocket\CookieConsent\Model\Cookie $cookie */
                $cookie = $this->cookieFactory->create();
            }
        } else {
            /** @var \Plumrocket\CookieConsent\Model\Cookie $cookie */
            $cookie = $this->cookieFactory->create();
        }

        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeFactory->create();
        $store->load($storeId);

        $cookie->setStoreId(null !== $store->getId() ? (int) $store->getId() : null);

        $this->cookieLocator->setStore($store);
        $this->cookieLocator->setCookie($cookie);

        return $cookie;
    }
}
