<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model\Guest\Access;

use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\ValidatorException;
use Plumrocket\DataPrivacy\Helper\Config;
use Plumrocket\DataPrivacy\Helper\Config\PrivacyCenterDashboard;
use Plumrocket\DataPrivacy\Model\Guest\PrivacyCenterToken;
use Plumrocket\Token\Api\CustomerHashValidatorInterface;

/**
 * Check if customer/guest have access.
 *
 * @since 3.1.0
 */
class Validator
{

    /**
     * @var \Plumrocket\DataPrivacy\Helper\Config
     */
    private $config;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Plumrocket\DataPrivacy\Helper\Config\PrivacyCenterDashboard
     */
    private $privacyCenterConfig;

    /**
     * @var \Plumrocket\Token\Api\CustomerHashValidatorInterface
     */
    private $tokenHashValidator;

    /**
     * @param \Plumrocket\DataPrivacy\Helper\Config                        $config
     * @param \Magento\Customer\Model\Session                              $customerSession
     * @param \Plumrocket\DataPrivacy\Helper\Config\PrivacyCenterDashboard $privacyCenterConfig
     * @param \Plumrocket\Token\Api\CustomerHashValidatorInterface         $tokenHashValidator
     */
    public function __construct(
        Config $config,
        Session $customerSession,
        PrivacyCenterDashboard $privacyCenterConfig,
        CustomerHashValidatorInterface $tokenHashValidator
    ) {
        $this->config = $config;
        $this->customerSession = $customerSession;
        $this->privacyCenterConfig = $privacyCenterConfig;
        $this->tokenHashValidator = $tokenHashValidator;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @throws \Magento\Framework\Exception\NotFoundException
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    public function validate(RequestInterface $request): void
    {
        if (! $this->config->isModuleEnabled()) {
            throw new NotFoundException(__('Data Privacy Center not found'));
        }

        if ($this->customerSession->isLoggedIn()) {
            return;
        }

        if (! $this->privacyCenterConfig->isAvailableToGuests()) {
            throw new ValidatorException(__('Privacy Center available only for registered customer, register please.'));
        }

        try {
            $this->tokenHashValidator->validate((string) $request->getParam('token', ''), PrivacyCenterToken::KEY);
            return;
        } catch (ValidatorException $validatorException) {
            throw new ValidatorException(__('Access deny because your token is invalid or expired.'));
        }
    }
}
