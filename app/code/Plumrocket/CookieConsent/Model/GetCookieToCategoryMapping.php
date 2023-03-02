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

namespace Plumrocket\CookieConsent\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Plumrocket\CookieConsent\Api\CookieRepositoryInterface;
use Plumrocket\CookieConsent\Api\GetCookieToCategoryMappingInterface;

/**
 * @since 1.0.0
 */
class GetCookieToCategoryMapping implements GetCookieToCategoryMappingInterface
{
    /**
     * @var \Plumrocket\CookieConsent\Api\CookieRepositoryInterface
     */
    private $cookieRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var null|string[]
     */
    private $mapping;

    /**
     * @param \Plumrocket\CookieConsent\Api\CookieRepositoryInterface $cookieRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder            $searchCriteriaBuilder
     */
    public function __construct(
        CookieRepositoryInterface $cookieRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->cookieRepository = $cookieRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritDoc
     */
    public function execute(): array
    {
        if (null === $this->mapping) {
            $searchResults = $this->cookieRepository->getList($this->searchCriteriaBuilder->create());

            $this->mapping = [];
            foreach ($searchResults->getItems() as $cookie) {
                $this->mapping[$cookie->getName()] = $cookie->getCategoryKey();
            }
        }

        return $this->mapping;
    }
}
