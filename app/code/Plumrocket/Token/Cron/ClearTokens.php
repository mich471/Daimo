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
 * @package     Plumrocket_Token
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Token\Cron;

class ClearTokens
{
    /**
     * @var \Plumrocket\Token\Api\CustomerRepositoryInterface
     */
    private $tokenRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * ClearTokens constructor.
     *
     * @param \Plumrocket\Token\Api\CustomerRepositoryInterface $tokenRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder      $searchCriteriaBuilder
     */
    public function __construct(
        \Plumrocket\Token\Api\CustomerRepositoryInterface $tokenRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->tokenRepository = $tokenRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Remove old tokens
     *
     * @throws \Exception
     */
    public function execute()
    {
        $todayDate = (new \DateTime())->format('Y-m-d');

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('expire_at', $todayDate, 'lt')
            ->create();

        $tokenSearchResults = $this->tokenRepository->getList($searchCriteria);

        foreach ($tokenSearchResults->getItems() as $token) {
            $this->tokenRepository->delete($token);
        }
    }
}
