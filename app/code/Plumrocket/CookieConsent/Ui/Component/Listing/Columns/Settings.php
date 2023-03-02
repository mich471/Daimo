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

namespace Plumrocket\CookieConsent\Ui\Component\Listing\Columns;

use Magento\Framework\Phrase;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\Ui\Component\Listing\Columns\Column;
use Plumrocket\CookieConsent\Api\Data\CategoryInterface;
use Plumrocket\CookieConsent\Api\Data\ConsentLogInterface;
use Plumrocket\CookieConsent\Api\GetEssentialCategoryKeysInterface;
use Plumrocket\CookieConsent\Model\Cookie\Attribute\Source\CategoryKey;

/**
 * @since 1.0.0
 */
class Settings extends Column
{
    /**
     * Store manager
     *
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @var \Plumrocket\CookieConsent\Model\Cookie\Attribute\Source\CategoryKey
     */
    private $categoryKey;

    /**
     * @var \Plumrocket\CookieConsent\Api\GetEssentialCategoryKeysInterface
     */
    private $getEssentialCategoryKeys;

    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface        $context
     * @param \Magento\Framework\View\Element\UiComponentFactory                  $uiComponentFactory
     * @param \Plumrocket\CookieConsent\Model\Cookie\Attribute\Source\CategoryKey $categoryKey
     * @param \Plumrocket\CookieConsent\Api\GetEssentialCategoryKeysInterface     $getEssentialCategoryKeys
     * @param array                                                               $components
     * @param array                                                               $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CategoryKey $categoryKey,
        GetEssentialCategoryKeysInterface $getEssentialCategoryKeys,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->categoryKey = $categoryKey;
        $this->getEssentialCategoryKeys = $getEssentialCategoryKeys;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    /**
     * Get data
     *
     * @param array $item
     * @return string|Phrase
     */
    protected function prepareItem(array $item)
    {
        $settings = (array) $item[ConsentLogInterface::SETTINGS];

        if (empty($settings)) {
            return __('Decline All');
        }

        if (isset($settings[CategoryInterface::ALL_CATEGORIES])) {
            return __('Accept All');
        }

        $categories = $this->categoryKey->toOptionHash();
        $essentialCategoryKeys = $this->getEssentialCategoryKeys->execute();

        $content = $this->getConsentsToExistingCategories($categories, $settings, $essentialCategoryKeys);
        $content .= $this->getConsentsToDeletedCategories($categories, $settings);

        return $content;
    }

    private function getConsentsToDeletedCategories(array $categories, array $settings): string
    {
        $content = '';
        foreach ($settings as $categoryKey => $isAllowed) {
            if (isset($categories[$categoryKey])) {
                continue;
            }

            $status = $isAllowed ? __('accepted') : __('declined');

            $content .= "{$categoryKey} (deleted): {$status}<br/>";
        }

        return $content;
    }

    /**
     * @param array $categories
     * @param array $settings
     * @param array $essentialCategoryKeys
     * @return string
     */
    protected function getConsentsToExistingCategories(
        array $categories,
        array $settings,
        array $essentialCategoryKeys
    ): string {
        $content = '';
        foreach ($categories as $categoryKey => $categoryName) {
            if (! isset($settings[$categoryKey])) {
                continue;
            }

            $status = $settings[$categoryKey] ? __('accepted') : __('declined');

            if (in_array($categoryKey, $essentialCategoryKeys, true)) {
                $content .= "<b>{$categoryName}</b>: {$status}<br/>";
            } else {
                $content .= "{$categoryName}: {$status}<br/>";
            }
        }

        return $content;
    }
}
