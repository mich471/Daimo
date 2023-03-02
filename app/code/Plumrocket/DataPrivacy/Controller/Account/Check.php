<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Controller\ResultFactory;
use Plumrocket\DataPrivacy\Helper\Config;
use Plumrocket\DataPrivacy\Helper\Config\PrivacyCenterDashboard;

/**
 * @since 3.1.0
 */
class Check extends Action
{
    /**
     * @var \Plumrocket\DataPrivacy\Helper\Config\PrivacyCenterDashboard
     */
    private $privacyCenterDashboard;

    /**
     * @var \Plumrocket\DataPrivacy\Helper\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private $httpContext;

    /**
     * @param \Magento\Framework\App\Action\Context                        $context
     * @param \Plumrocket\DataPrivacy\Helper\Config\PrivacyCenterDashboard $privacyCenterDashboard
     * @param \Plumrocket\DataPrivacy\Helper\Config                        $config
     * @param \Magento\Framework\App\Http\Context                          $httpContext
     */
    public function __construct(
        Context $context,
        PrivacyCenterDashboard $privacyCenterDashboard,
        Config $config,
        HttpContext $httpContext
    ) {
        parent::__construct($context);
        $this->privacyCenterDashboard = $privacyCenterDashboard;
        $this->config = $config;
        $this->httpContext = $httpContext;
    }

    /**
     * Performs check action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (! $this->config->isModuleEnabled()) {
            return $this->resultFactory->create(ResultFactory::TYPE_FORWARD)->forward('noroute');
        }

        if ($this->isLoggedIn()) {
            return $this->resultRedirectFactory->create()->setPath('prgdpr/account');
        }

        if (! $this->privacyCenterDashboard->isAvailableToGuests()) {
            return $this->resultRedirectFactory->create()->setPath('prgdpr/account');
        }

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }

    /**
     * Check if customer is logged in.
     *
     * @return bool
     */
    private function isLoggedIn(): bool
    {
        return (bool) $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }
}
