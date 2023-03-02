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

namespace Plumrocket\CookieConsent\Controller\Adminhtml\Category;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\StoreManagerInterface;
use Plumrocket\CookieConsent\Api\CategoryRepositoryInterface;
use Plumrocket\CookieConsent\Model\Category;
use Plumrocket\CookieConsent\Model\CategoryFactory;
use Plumrocket\CookieConsent\Ui\Locator\CategoryLocatorInterface;

/**
 * @since 1.0.0
 */
class Builder
{
    /**
     * @var \Plumrocket\CookieConsent\Model\CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Store\Model\StoreFactory
     */
    private $storeFactory;

    /**
     * @var \Plumrocket\CookieConsent\Ui\Locator\CategoryLocatorInterface
     */
    private $categoryLocator;

    /**
     * @var \Plumrocket\CookieConsent\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @param \Plumrocket\CookieConsent\Model\CategoryFactory               $categoryFactory
     * @param \Magento\Store\Model\StoreManagerInterface                    $storeManager
     * @param \Magento\Store\Model\StoreFactory                             $storeFactory
     * @param \Plumrocket\CookieConsent\Ui\Locator\CategoryLocatorInterface $categoryLocator
     * @param \Plumrocket\CookieConsent\Api\CategoryRepositoryInterface     $categoryRepository
     */
    public function __construct(
        CategoryFactory $categoryFactory,
        StoreManagerInterface $storeManager,
        StoreFactory $storeFactory,
        CategoryLocatorInterface $categoryLocator,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->storeManager = $storeManager;
        $this->storeFactory = $storeFactory;
        $this->categoryLocator = $categoryLocator;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Build cookie category based on user request
     *
     * @param RequestInterface $request
     * @return \Plumrocket\CookieConsent\Model\Category
     */
    public function build(RequestInterface $request): Category
    {
        $storeId = (int) $request->getParam('store', 0);
        $categoryId = (int) $request->getParam('id');

        $store = $this->storeManager->getStore($storeId);
        $this->storeManager->setCurrentStore($store->getCode());

        if ($categoryId) {
            try {
                $category = $this->categoryRepository->getById($categoryId, $storeId);
            } catch (NoSuchEntityException $e) {
                /** @var \Plumrocket\CookieConsent\Model\Category $category */
                $category = $this->categoryFactory->create();
            }
        } else {
            /** @var \Plumrocket\CookieConsent\Model\Category $category */
            $category = $this->categoryFactory->create();
        }

        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeFactory->create();
        $store->load($storeId);

        $category->setStoreId(null !== $store->getId() ? (int) $store->getId() : null);

        $this->categoryLocator->setStore($store);
        $this->categoryLocator->setCategory($category);

        return $category;
    }
}
