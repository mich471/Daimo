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

namespace Plumrocket\CookieConsent\Model\Consent;

use Plumrocket\CookieConsent\Api\Data\CategoryInterface;
use Plumrocket\CookieConsent\Api\GetEssentialCategoryKeysInterface;
use Plumrocket\CookieConsent\Model\Category\Source\EnabledCategories;

/**
 * @since 1.0.0
 */
class CreateSettingsFromParam
{
    /**
     * @var \Plumrocket\CookieConsent\Model\Category\Source\EnabledCategories
     */
    private $enabledCategories;

    /**
     * @var \Plumrocket\CookieConsent\Api\GetEssentialCategoryKeysInterface
     */
    private $getEssentialCategoryKeys;

    /**
     * @param \Plumrocket\CookieConsent\Model\Category\Source\EnabledCategories $enabledCategories
     * @param \Plumrocket\CookieConsent\Api\GetEssentialCategoryKeysInterface   $getEssentialCategoryKeys
     */
    public function __construct(
        EnabledCategories $enabledCategories,
        GetEssentialCategoryKeysInterface $getEssentialCategoryKeys
    ) {
        $this->enabledCategories = $enabledCategories;
        $this->getEssentialCategoryKeys = $getEssentialCategoryKeys;
    }

    /**
     * @param array $acceptedKeys
     * @return bool[]
     */
    public function execute(array $acceptedKeys): array
    {
        if (empty($acceptedKeys)) {
            return [];
        }

        if (in_array(CategoryInterface::ALL_CATEGORIES, $acceptedKeys, true)) {
            $settings = [CategoryInterface::ALL_CATEGORIES => true];
        } else {
            $enabledCategoriesKeys = array_keys($this->enabledCategories->toOptionHash());
            $essentialCategoryKeys = $this->getEssentialCategoryKeys->execute();

            $settings = [];
            foreach ($enabledCategoriesKeys as $enabledCategoriesKey) {
                if (in_array($enabledCategoriesKey, $essentialCategoryKeys, true)) {
                    $settings[$enabledCategoriesKey] = true;
                } else {
                    $settings[$enabledCategoriesKey] = in_array($enabledCategoriesKey, $acceptedKeys, true);
                }
            }
        }

        return $settings;
    }
}
