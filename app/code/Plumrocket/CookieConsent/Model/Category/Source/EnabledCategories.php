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

namespace Plumrocket\CookieConsent\Model\Category\Source;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Data\OptionSourceInterface;
use Plumrocket\CookieConsent\Api\CategoryRepositoryInterface;
use Plumrocket\CookieConsent\Api\Data\CategoryInterface;

/**
 * @since 1.0.0
 */
class EnabledCategories implements OptionSourceInterface
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
     * @var array|null
     */
    private $optionHash;

    /**
     * CategoryKey constructor.
     *
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
     * Return array of options
     *
     * @return array Format: array(array('<value>' => '<label>'), ...)
     */
    public function toOptionHash(): array
    {
        if (null === $this->optionHash) {
            $categories = $this->categoryRepository
                ->getList($this->searchCriteriaBuilder->addFilter(CategoryInterface::STATUS, 1)->create())
                ->getItems();

            $this->optionHash = [];
            foreach ($categories as $category) {
                $this->optionHash[$category->getKey()] = $category->getName();
            }
        }

        return $this->optionHash;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        $result = [];
        foreach ($this->toOptionHash() as $key => $value) {
            $result[] = [
                'value'    => $key,
                'label'    => $value,
            ];
        }

        return $result;
    }
}
