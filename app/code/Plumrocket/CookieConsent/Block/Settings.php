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

namespace Plumrocket\CookieConsent\Block;

use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Plumrocket\CookieConsent\Api\CanManageCookieInterface;
use Plumrocket\CookieConsent\Api\CategoryRepositoryInterface;
use Plumrocket\CookieConsent\Api\CookieRepositoryInterface;
use Plumrocket\CookieConsent\Api\Data\CategoryInterface;
use Plumrocket\CookieConsent\Api\Data\CookieInterface;
use Plumrocket\CookieConsent\Helper\Config\SettingsBar as SettingsBarConfig;
use Plumrocket\CookieConsent\Model\ResourceModel\Category as CategoryResource;
use Plumrocket\CookieConsent\ViewModel\ExtractCategoryData;
use Plumrocket\CookieConsent\ViewModel\ExtractCookieData;

/**
 * @since 1.0.0
 */
class Settings extends Template
{
    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Plumrocket\CookieConsent\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Plumrocket\CookieConsent\Api\CanManageCookieInterface
     */
    private $canManageCookie;

    /**
     * @var \Magento\Framework\Api\SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var \Plumrocket\CookieConsent\Api\CookieRepositoryInterface
     */
    private $cookieRepository;

    /**
     * @var \Plumrocket\CookieConsent\ViewModel\ExtractCookieData
     */
    private $extractCookieData;

    /**
     * @var \Plumrocket\CookieConsent\ViewModel\ExtractCategoryData
     */
    private $extractCategoryData;

    /**
     * @var \Plumrocket\CookieConsent\Helper\Config\SettingsBar
     */
    private $settingsBarConfig;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    private $filterProvider;

    /**
     * @param \Magento\Framework\View\Element\Template\Context          $context
     * @param \Magento\Framework\Serialize\SerializerInterface          $serializer
     * @param \Plumrocket\CookieConsent\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder              $searchCriteriaBuilder
     * @param \Plumrocket\CookieConsent\Api\CanManageCookieInterface    $canManageCookie
     * @param \Magento\Framework\Api\SortOrderBuilder                   $sortOrderBuilder
     * @param \Plumrocket\CookieConsent\Api\CookieRepositoryInterface   $cookieRepository
     * @param \Plumrocket\CookieConsent\ViewModel\ExtractCookieData     $extractCookieData
     * @param \Plumrocket\CookieConsent\ViewModel\ExtractCategoryData   $extractCategoryData
     * @param \Plumrocket\CookieConsent\Helper\Config\SettingsBar       $settingsBarConfig
     * @param \Magento\Cms\Model\Template\FilterProvider                $filterProvider
     * @param array                                                     $data
     */
    public function __construct(
        Context $context,
        SerializerInterface $serializer,
        CategoryRepositoryInterface $categoryRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CanManageCookieInterface $canManageCookie,
        SortOrderBuilder $sortOrderBuilder,
        CookieRepositoryInterface $cookieRepository,
        ExtractCookieData $extractCookieData,
        ExtractCategoryData $extractCategoryData,
        SettingsBarConfig $settingsBarConfig,
        FilterProvider $filterProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->serializer = $serializer;
        $this->categoryRepository = $categoryRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->canManageCookie = $canManageCookie;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->cookieRepository = $cookieRepository;
        $this->extractCookieData = $extractCookieData;
        $this->extractCategoryData = $extractCategoryData;
        $this->settingsBarConfig = $settingsBarConfig;
        $this->filterProvider = $filterProvider;
    }

    /**
     * @return array
     */
    public function getJsComponentConfig(): array
    {
        return [
            'categories' => $this->getCategoriesData(),
            'cookies' => $this->getCookiesData(),
            'canShowCookieDetails' => $this->settingsBarConfig->canShowCookieDetails(),
            'overview' => [
                'title' => $this->settingsBarConfig->getOverviewTitle(),
                'text' => $this->filterProvider->getPageFilter()->filter($this->settingsBarConfig->getOverviewText()),
            ],
            'design' => [
                'textColor' => $this->settingsBarConfig->getTextColor(),
                'backgroundColor' => $this->settingsBarConfig->getBackgroundColor(),
            ],
            'acceptButtonConfig' => $this->prepareButtonConfig($this->settingsBarConfig->getAcceptButtonConfig()),
            'declineButtonConfig' => $this->prepareButtonConfig($this->settingsBarConfig->getDeclineButtonConfig()),
            'confirmButtonConfig' => $this->prepareButtonConfig($this->settingsBarConfig->getConfirmButtonConfig()),
        ];
    }

    /**
     * @return bool
     */
    public function canManageCookie(): bool
    {
        return $this->canManageCookie->execute();
    }

    /**
     * @return false|string
     */
    public function getJsLayout()
    {
        if (isset($this->jsLayout['components']['pr-cookie-settings-bar'])) {
            $this->jsLayout['components']['pr-cookie-settings-bar'] = array_merge_recursive(
                $this->jsLayout['components']['pr-cookie-settings-bar'],
                $this->getJsComponentConfig()
            );
        } else {
            $this->jsLayout['components']['pr-cookie-settings-bar'] = $this->getJsComponentConfig();
        }

        return $this->jsLayout ? $this->serializer->serialize($this->jsLayout) : '';
    }

    /**
     * @return array[]
     */
    private function getCategoriesData(): array
    {
        $extractCategoryData = $this->extractCategoryData;
        return array_values(array_map(static function (CategoryInterface $category) use ($extractCategoryData) {
            return $extractCategoryData->execute($category);
        }, $this->getCategories()));
    }

    /**
     * @return array[]
     */
    private function getCookiesData(): array
    {
        $categoriesKeys = array_map(static function (CategoryInterface $category) {
            return $category->getKey();
        }, $this->getCategories());

        $this->searchCriteriaBuilder->addFilter(
            CookieInterface::CATEGORY_KEY,
            $categoriesKeys,
            'in'
        );

        $result = $this->cookieRepository->getList($this->searchCriteriaBuilder->create());

        $extractCookieData = $this->extractCookieData;
        return array_values(array_map(static function (CookieInterface $cookie) use ($extractCookieData) {
            return $extractCookieData->execute($cookie);
        }, $result->getItems()));
    }

    /**
     * @return \Plumrocket\CookieConsent\Api\Data\CategoryInterface[]
     */
    private function getCategories(): array
    {
        $this->searchCriteriaBuilder->addFilter('status', 1);
        $this->searchCriteriaBuilder->addSortOrder(
            $this->sortOrderBuilder
                ->setField(CategoryInterface::SORT_ORDER)
                ->setAscendingDirection()
                ->create()
        );
        $this->searchCriteriaBuilder->addSortOrder(
            $this->sortOrderBuilder
                ->setField(CategoryInterface::CREATED_AT)
                ->setAscendingDirection()
                ->create()
        );
        // During installation all categories will have same created_at so, order by id keep right order for this case
        $this->searchCriteriaBuilder->addSortOrder(
            $this->sortOrderBuilder
                ->setField(CategoryResource::ID_FIELD_NAME)
                ->setAscendingDirection()
                ->create()
        );

        return $this->categoryRepository->getList($this->searchCriteriaBuilder->create())->getItems();
    }

    /**
     * @param array $config
     * @return array
     */
    private function prepareButtonConfig(array $config): array
    {
        if (isset($config['enabled'])) {
            $config['enabled'] = (bool) $config['enabled'];
        }

        return $config;
    }
}
