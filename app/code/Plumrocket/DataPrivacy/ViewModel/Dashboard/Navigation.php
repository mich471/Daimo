<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\ViewModel\Dashboard;

use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Plumrocket\DataPrivacy\Model\Guest\Access\TokenLocator;
use Plumrocket\Token\Api\CustomerRepositoryInterface;

class Navigation implements ArgumentInterface
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Plumrocket\DataPrivacy\Model\Guest\Access\TokenLocator
     */
    private $tokenLocator;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private $httpContext;

    /**
     * @var \Plumrocket\Token\Api\CustomerRepositoryInterface
     */
    private $tokenRepository;

    /**
     * @param \Magento\Framework\UrlInterface                         $urlBuilder
     * @param \Plumrocket\DataPrivacy\Model\Guest\Access\TokenLocator $tokenLocator
     * @param \Magento\Framework\App\Http\Context                     $httpContext
     * @param \Plumrocket\Token\Api\CustomerRepositoryInterface       $tokenRepository
     */
    public function __construct(
        UrlInterface $urlBuilder,
        TokenLocator $tokenLocator,
        HttpContext $httpContext,
        CustomerRepositoryInterface $tokenRepository
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->tokenLocator = $tokenLocator;
        $this->httpContext = $httpContext;
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * Get export page url.
     *
     * @return string
     */
    public function getExportPageUrl(): string
    {
        return $this->urlBuilder->getUrl('pr_data_privacy/account/export', $this->getUrlParams());
    }

    /**
     * Get delete page url.
     *
     * @return string
     */
    public function getDeletingPageUrl(): string
    {
        return $this->urlBuilder->getUrl('pr_data_privacy/account/delete', $this->getUrlParams());
    }

    /**
     * @return array
     */
    private function getUrlParams(): array
    {
        $token = $this->tokenLocator->getToken();
        return $token ? ['token' => $token] : [];
    }

    /**
     * @return bool
     */
    public function isCustomerLoggedIn(): bool
    {
        return (bool) $this->httpContext->getValue(CustomerContext::CONTEXT_AUTH);
    }

    /**
     * Get guest email by token.
     *
     * @return string
     */
    public function getGuestEmail(): string
    {
        try {
            return $this->tokenRepository->get($this->tokenLocator->getToken())->getEmail();
        } catch (NoSuchEntityException $e) {
            return '';
        }
    }
}
