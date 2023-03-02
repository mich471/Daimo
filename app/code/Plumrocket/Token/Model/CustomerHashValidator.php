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

namespace Plumrocket\Token\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\ValidatorException;
use Plumrocket\Token\Api\CustomerHashValidatorInterface as TokenCustomerHashValidatorInterface;
use Plumrocket\Token\Api\CustomerRepositoryInterface as CustomerTokenRepositoryInterface;
use Plumrocket\Token\Api\CustomerValidatorInterface as CustomerTokenValidatorInterface;

class CustomerHashValidator implements TokenCustomerHashValidatorInterface
{
    /**
     * @var \Plumrocket\Token\Api\CustomerRepositoryInterface
     */
    private $tokenRepository;

    /**
     * @var \Plumrocket\Token\Api\CustomerValidatorInterface
     */
    private $tokenValidator;

    /**
     * TokenHashValidator constructor.
     *
     * @param \Plumrocket\Token\Api\CustomerRepositoryInterface $tokenRepository
     * @param \Plumrocket\Token\Api\CustomerValidatorInterface  $tokenValidator
     */
    public function __construct(
        CustomerTokenRepositoryInterface $tokenRepository,
        CustomerTokenValidatorInterface $tokenValidator
    ) {
        $this->tokenRepository = $tokenRepository;
        $this->tokenValidator = $tokenValidator;
    }

    /**
     * @param string $tokenHash
     * @param string $typeKey
     * @return bool
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    public function validate(string $tokenHash, string $typeKey = ''): bool
    {
        try {
            $token = $this->tokenRepository->get($tokenHash);

            return $this->tokenValidator->validate($token, $typeKey);
        } catch (NoSuchEntityException $e) {
            throw new ValidatorException(__('Token is invalid.'));
        }
    }
}
