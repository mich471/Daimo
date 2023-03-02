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

namespace Plumrocket\CookieConsent\Model\Category;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Plumrocket\CookieConsent\Api\CategoryRepositoryInterface;
use Plumrocket\CookieConsent\Api\Data\CategoryInterface;
use Plumrocket\CookieConsent\Api\GetEssentialCategoryKeysInterface;

/**
 * @since 1.0.0
 */
class GetEssentialKeys implements GetEssentialCategoryKeysInterface
{
    /**
     * @var \Plumrocket\CookieConsent\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Contain essential categories keys
     *
     * @var null|string[]
     */
    private $localCache;

    /**
     * @param \Plumrocket\CookieConsent\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder              $searchCriteriaBuilder
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritDoc
     */
    public function execute(): array
    {
        if (null === $this->localCache) {
            $this->searchCriteriaBuilder->addFilter(CategoryInterface::STATUS, 1);
            $this->searchCriteriaBuilder->addFilter(CategoryInterface::IS_ESSENTIAL, 1);

            $searchResults = $this->categoryRepository->getList($this->searchCriteriaBuilder->create());

            $this->localCache = [];
            foreach ($searchResults->getItems() as $category) {
                $this->localCache[] = $category->getKey();
            }
        }

        return $this->localCache;
    }
}
