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

namespace Plumrocket\CookieConsent\ViewModel;

use Plumrocket\CookieConsent\Api\Data\CategoryInterface;

/**
 * @since 1.0.0
 */
class ExtractCategoryData
{
    /**
     * @param \Plumrocket\CookieConsent\Api\Data\CategoryInterface $category
     * @return array
     */
    public function execute(CategoryInterface $category): array
    {
        return [
            CategoryInterface::KEY            => $category->getKey(),
            CategoryInterface::IS_ESSENTIAL   => $category->isEssential(),
            CategoryInterface::IS_PRE_CHECKED => $category->isPreChecked(),
            CategoryInterface::NAME           => $category->getName(),
            CategoryInterface::DESCRIPTION    => $category->getDescription(),
            CategoryInterface::SORT_ORDER     => $category->getSortOrder(),
        ];
    }
}
