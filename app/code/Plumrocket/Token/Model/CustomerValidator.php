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

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Plumrocket\Token\Api\CustomerValidatorInterface as TokenCustomerValidatorInterface;
use Plumrocket\Token\Api\Data\CustomerInterface;

class CustomerValidator implements TokenCustomerValidatorInterface
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * TokenValidator constructor.
     *
     * @param \Magento\Framework\Stdlib\DateTime\DateTime       $dateTime
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        DateTime $dateTime,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->dateTime = $dateTime;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param \Plumrocket\Token\Api\Data\CustomerInterface $token
     * @param string                                       $typeKey @since 1.0.3 will be required in next major version
     * @return bool
     * @throws \Magento\Framework\Exception\ValidatorException
     * @throws \Exception
     */
    public function validate(CustomerInterface $token, string $typeKey = '') : bool
    {
        if ($token->getId() && $token->getHash() && (! $typeKey || $token->getTypeKey() === $typeKey)) {
            $expireDate = new \DateTime($token->getExpireAt());
            $today = new \DateTime($this->dateTime->gmtDate('Y-m-d'));
            if ($expireDate >= $today) {
                if (! $token->getCustomerId()) {
                    return true;
                }

                try {
                    $this->customerRepository->getById($token->getCustomerId());
                    return true;
                } catch (LocalizedException $e) {
                    throw $e;
                }
            }
        }

        throw new ValidatorException(__('Token is invalid.'));
    }
}
