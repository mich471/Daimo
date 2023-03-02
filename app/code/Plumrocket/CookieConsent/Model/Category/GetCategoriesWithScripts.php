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

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Plumrocket\CookieConsent\Api\CategoryRepositoryInterface;
use Plumrocket\CookieConsent\Api\Data\CategoryInterface;

/**
 * @since 1.0.0
 */
class GetCategoriesWithScripts
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
     * @var \Plumrocket\CookieConsent\Api\Data\CategoryInterface[]
     */
    private $localCache;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $filterBuilder;

    /**
     * @param \Plumrocket\CookieConsent\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder              $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder                      $filterBuilder
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * @return \Plumrocket\CookieConsent\Api\Data\CategoryInterface[]
     */
    public function execute(): array
    {
        if (null === $this->localCache) {
            $this->searchCriteriaBuilder->addFilter(CategoryInterface::STATUS, 1);

            $notEmptyHeadScriptsFilter = $this->filterBuilder
                ->setField(CategoryInterface::HEAD_SCRIPTS)
                ->setValue('')
                ->setConditionType('neq')
                ->create();
            $notEmptyFooterHtmlFilter = $this->filterBuilder
                ->setField(CategoryInterface::FOOTER_MISCELLANEOUS_HTML)
                ->setValue('')
                ->setConditionType('neq')
                ->create();

            $this->searchCriteriaBuilder->addFilters([$notEmptyHeadScriptsFilter, $notEmptyFooterHtmlFilter]);

            $searchResults = $this->categoryRepository->getList($this->searchCriteriaBuilder->create());

            $this->localCache = $searchResults->getItems();
        }

        return $this->localCache;
    }
}
