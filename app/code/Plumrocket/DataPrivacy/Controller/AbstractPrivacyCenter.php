<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Controller;

use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\ValidatorException;
use Plumrocket\DataPrivacy\Model\Guest\Access\TokenLocator;
use Plumrocket\DataPrivacy\Model\Guest\Access\Validator;

/**
 * Check if customer/guest have access to Privacy center and its functionality.
 *
 * @since 3.1.0
 */
abstract class AbstractPrivacyCenter extends Action
{

    /**
     * @var \Plumrocket\DataPrivacy\Model\Guest\Access\Validator
     */
    private $accessValidator;

    /**
     * @var CustomerUrl
     */
    private $customerUrl;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private $httpContext;

    /**
     * @var \Plumrocket\DataPrivacy\Model\Guest\Access\TokenLocator
     */
    private $tokenLocator;

    /**
     * @param \Magento\Framework\App\Action\Context                   $context
     * @param \Plumrocket\DataPrivacy\Model\Guest\Access\Validator    $accessValidator
     * @param \Magento\Customer\Model\Url                             $customerUrl
     * @param \Magento\Framework\App\Http\Context                     $httpContext
     * @param \Plumrocket\DataPrivacy\Model\Guest\Access\TokenLocator $tokenLocator
     */
    public function __construct(
        Context $context,
        Validator $accessValidator,
        CustomerUrl $customerUrl,
        \Magento\Framework\App\Http\Context $httpContext,
        TokenLocator $tokenLocator
    ) {
        parent::__construct($context);
        $this->accessValidator = $accessValidator;
        $this->customerUrl = $customerUrl;
        $this->httpContext = $httpContext;
        $this->tokenLocator = $tokenLocator;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function dispatch(RequestInterface $request)
    {
        try {
            $this->accessValidator->validate($request);
            if (! $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH)) {
                $token = $this->getRequest()->getParam('token', '');
                $this->tokenLocator->setToken($token);
            }
            return parent::dispatch($request);
        } catch (NotFoundException $e) {
            $forwardResult = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            return $forwardResult->forward('no_route');
        } catch (ValidatorException $e) {
            $this->messageManager->addWarningMessage($e->getMessage());
            $redirectResult = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $redirectResult->setPath(
                CustomerUrl::ROUTE_ACCOUNT_LOGIN,
                $this->customerUrl->getLoginUrlParams()
            );
        }
    }

    /**
     * @return bool
     */
    protected function isLoggedIn(): bool
    {
        return (bool) $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    /**
     * @return string
     */
    protected function getTokenHash(): string
    {
        return $this->tokenLocator->getToken();
    }
}
