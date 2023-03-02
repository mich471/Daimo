<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\ViewModel\Dashboard;

use Magento\Customer\Model\Url;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Plumrocket\DataPrivacy\Model\Guest\Access\TokenLocator;

/**
 * @since 3.1.0
 */
class UserAction implements ArgumentInterface
{

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Plumrocket\DataPrivacy\Model\Guest\Access\TokenLocator
     */
    private $tokenLocator;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private $httpContext;

    /**
     * @var \Magento\Customer\Model\Url
     */
    private $customerUrl;

    /**
     * @param \Magento\Framework\UrlInterface                         $urlBuilder
     * @param \Plumrocket\DataPrivacy\Model\Guest\Access\TokenLocator $tokenLocator
     * @param \Magento\Framework\App\Http\Context                     $httpContext
     * @param \Magento\Customer\Model\Url                             $customerUrl
     */
    public function __construct(
        UrlInterface $urlBuilder,
        TokenLocator $tokenLocator,
        HttpContext $httpContext,
        Url $customerUrl
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->tokenLocator = $tokenLocator;
        $this->httpContext = $httpContext;
        $this->customerUrl = $customerUrl;
    }

    public function isLoggedIn(): bool
    {
        return (bool) $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    /**
     * @return string
     */
    public function goBackUrl(): string
    {
        if ($this->isLoggedIn()) {
            return $this->urlBuilder->getUrl('pr_data_privacy/account/index');
        }
        return $this->urlBuilder->getUrl('pr_data_privacy/account/index', ['token' => $this->getSecureToken()]);
    }

    /**
     * Get forgot password page url.
     *
     * @return string
     */
    public function getForgotPasswordUrl(): string
    {
        return $this->customerUrl->getForgotPasswordUrl();
    }

    /**
     * @return string
     */
    public function getSecureToken(): string
    {
        return $this->tokenLocator->getToken();
    }
}
