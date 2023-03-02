<?php
/**
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Plumrocket\CookieConsent\Api\CategoryRepositoryInterface;
use Plumrocket\CookieConsent\Api\Data\CategoryInterfaceFactory;
use Plumrocket\CookieConsent\Model\Category\Attribute\Source\CategoryKey;
use Plumrocket\CookieConsent\Model\ResourceModel\Category\GetKeys;

/**
 * @since 1.3.0
 */
class CreateDefaultCategories implements DataPatchInterface
{
    /**
     * @var \Plumrocket\CookieConsent\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var \Plumrocket\CookieConsent\Api\Data\CategoryInterfaceFactory
     */
    private $categoryFactory;

    /**
     * @var \Plumrocket\CookieConsent\Model\ResourceModel\Category\GetKeys
     */
    private $getCreatedCategoriesKeys;

    /**
     * @param \Plumrocket\CookieConsent\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Plumrocket\CookieConsent\Api\Data\CategoryInterfaceFactory $categoryFactory
     * @param \Plumrocket\CookieConsent\Model\ResourceModel\Category\GetKeys $getCreatedCategoriesKeys
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        CategoryInterfaceFactory $categoryFactory,
        GetKeys $getCreatedCategoriesKeys
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $categoryFactory;
        $this->getCreatedCategoriesKeys = $getCreatedCategoriesKeys;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $createdCategoriesKeys = $this->getCreatedCategoriesKeys->execute();

        foreach ($this->getCategories() as $categoryData) {
            if (in_array($categoryData['key'], $createdCategoriesKeys, true)) {
                continue;
            }

            /** @var \Plumrocket\CookieConsent\Api\Data\CategoryInterface $category */
            $category = $this->categoryFactory->create();
            $category->setName($categoryData['name']);
            $category->setIsEssential($categoryData['isEssential']);
            $category->setKey($categoryData['key']);
            $category->setStatus(true);
            $category->setSortOrder($categoryData['sortOrder']);
            $category->setDescription($categoryData['description']);
            $category->setStoreId(0); // save on default level

            $this->categoryRepository->save($category);
        }
    }

    /**
     * Get default cookie categories settings
     *
     * @return array[]
     */
    private function getCategories(): array
    {
        return [
            [
                'name' => 'Strictly necessary cookies',
                'isEssential' => true,
                'key' => CategoryKey::KEY_NECESSARY,
                'sortOrder' => 0,
                'description' => 'These cookies are essential for you to browse our store and use its features, ' .
                    'such as accessing secure areas of the website. Cookies that allow holding your cart items, ' .
                    'cookies that keep you logged-in and cookies that save your customized preferences are an ' .
                    'example of strictly necessary cookies. These cookies are essential to a website\'s ' .
                    'functionality and cannot be disabled by users.',
            ],
            [
                'name' => 'Preferences cookies',
                'isEssential' => false,
                'key' => CategoryKey::KEY_PREFERENCES,
                'sortOrder' => 0,
                'description' => 'Preferences cookies are also known as “functionality cookies”. These cookies ' .
                    'allow a website to remember choices you have made in the past, like what language you prefer, ' .
                    'your favourite search filters, or what your user name and password are so you can automatically ' .
                    'log in.',
            ],
            [
                'name' => 'Statistics cookies',
                'isEssential' => false,
                'key' => CategoryKey::KEY_STATISTICS,
                'sortOrder' => 0,
                'description' => 'Statistics cookies are also known as “performance cookies”. These cookies collect ' .
                    'information about how you use a website, like which pages you visited and which links you ' .
                    'clicked on. None of this information can be used to identify you. It is all aggregated and, ' .
                    'therefore, anonymized. Their sole purpose is to improve website functions. This includes ' .
                    'cookies from third-party analytics services, such as visitor analytics, heatmaps and social ' .
                    'media analytics.',
            ],
            [
                'name' => 'Marketing cookies',
                'isEssential' => false,
                'key' => CategoryKey::KEY_MARKETING,
                'sortOrder' => 0,
                'description' => 'These cookies track your online activity to help advertisers deliver more relevant ' .
                    'advertising or to limit how many times you see an ad. These cookies can share that information ' .
                    'with other organizations or advertisers. These are persistent cookies and almost always of ' .
                    'third-party provenance.',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [\Plumrocket\CookieConsent\Setup\Patch\Data\CreateEntities::class];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
