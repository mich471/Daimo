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

namespace Plumrocket\CookieConsent\Block\Html;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Plumrocket\CookieConsent\Api\Data\CategoryInterface;
use Plumrocket\CookieConsent\Model\Category\GetCategoriesWithScripts;

/**
 * @since 1.0.0
 */
class BodyScripts extends Template
{
    /**
     * @var \Plumrocket\CookieConsent\Model\Category\GetCategoriesWithScripts
     */
    private $getCategoriesWithScripts;

    /**
     * @param \Magento\Framework\View\Element\Template\Context                  $context
     * @param \Plumrocket\CookieConsent\Model\Category\GetCategoriesWithScripts $getCategoriesWithScripts
     * @param array                                                             $data
     */
    public function __construct(
        Context $context,
        GetCategoriesWithScripts $getCategoriesWithScripts,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->getCategoriesWithScripts = $getCategoriesWithScripts;
    }

    /**
     * @return \Plumrocket\CookieConsent\Api\Data\CategoryInterface[]
     */
    public function getEssentialCategoriesWithScripts(): array
    {
        return array_filter(
            $this->getCategoriesWithScripts->execute(),
            static function (CategoryInterface $category) {
                return $category->isEssential();
            }
        );
    }

    /**
     * @return \Plumrocket\CookieConsent\Api\Data\CategoryInterface[]
     */
    public function getOptionalCategoriesWithScripts(): array
    {
        return array_filter(
            $this->getCategoriesWithScripts->execute(),
            static function (CategoryInterface $category) {
                return ! $category->isEssential();
            }
        );
    }

    /**
     * @param string $html
     * @return string
     */
    public function prepareHtml(string $html): string
    {
        return str_replace(
            ['\'', '<script', '</script>', "\n","\t","\r", ],
            ['\\\'', '<scr\' + \'ipt', '</scr\' + \'ipt>', ' ', ' ', ' ', ],
            $html
        );
    }
}
