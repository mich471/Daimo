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

class GenerateForCustomer implements \Plumrocket\Token\Api\GenerateForCustomerInterface
{
    /**
     * @var \Plumrocket\Token\Model\CustomerFactory
     */
    private $tokenFactory;

    /**
     * @var \Plumrocket\Token\Api\TokenHashGeneratorInterface
     */
    private $generateHash;

    /**
     * @var \Plumrocket\Token\Model\ResourceModel\Customer\GetTokenHashList
     */
    private $getTokenHashList;

    /**
     * @var \Plumrocket\Token\Api\TokenRepositoryInterface
     */
    private $tokenRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Plumrocket\Token\Api\CustomerTypePoolInterface
     */
    private $customerTypes;

    /**
     * @var \Magento\Framework\Api\SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var array
     */
    private $localCache = [];

    /**
     * GenerateForCustomer constructor.
     *
     * @param \Plumrocket\Token\Model\CustomerFactory $tokenFactory
     * @param \Plumrocket\Token\Api\GenerateHashInterface $generateHash
     * @param \Plumrocket\Token\Model\ResourceModel\Customer\GetTokenHashList $getTokenHashList
     * @param \Plumrocket\Token\Api\CustomerRepositoryInterface $tokenRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Plumrocket\Token\Api\CustomerTypePoolInterface $customerTypes
     * @param \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        \Plumrocket\Token\Model\CustomerFactory $tokenFactory,
        \Plumrocket\Token\Api\GenerateHashInterface $generateHash,
        \Plumrocket\Token\Model\ResourceModel\Customer\GetTokenHashList $getTokenHashList,
        \Plumrocket\Token\Api\CustomerRepositoryInterface $tokenRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Plumrocket\Token\Api\CustomerTypePoolInterface $customerTypes,
        \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder
    ) {
        $this->tokenFactory = $tokenFactory;
        $this->generateHash = $generateHash;
        $this->getTokenHashList = $getTokenHashList;
        $this->tokenRepository = $tokenRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerTypes = $customerTypes;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * Generate token by type and set authentication info
     *
     * @param int    $customerId
     * @param string $email
     * @param string $typeKey
     * @return \Plumrocket\Token\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\SecurityViolationException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(
        int $customerId,
        string $email,
        string $typeKey,
        array $additionalData = []
    ) : \Plumrocket\Token\Api\Data\CustomerInterface {
        $type = $this->customerTypes->getByType($typeKey);
        $todayDate = (new \DateTime())->format('Y-m-d');

        if ($token = $this->getTokenByDate($customerId, $email, $todayDate, $type->getKey())) {
            return $token;
        }

        /** @var \Plumrocket\Token\Api\Data\CustomerInterface $token */
        $token = $this->tokenFactory->create();

        $hashes = $this->getTokenHashList->execute();
        do {
            $tokenHash = $this->generateHash->execute();
        } while (in_array($tokenHash, $hashes, true));

        $expireDate = (new \DateTime())
            ->add(new \DateInterval('P' . $type->getLifetimeDays() . 'D'))
            ->format('Y-m-d');

        $token->setHash($tokenHash);
        $token->setCustomerId($customerId);
        $token->setEmail($email);
        $token->setCreateAt($todayDate);
        $token->setExpireAt($expireDate);
        $token->setTypeKey($type->getKey());
        $token->setAdditionalData($additionalData);

        return $this->tokenRepository->save($token);
    }

    /**
     * @param int    $customerId
     * @param string $recipientEmail
     * @param string $createDate
     * @param string $typeKey
     * @return bool|mixed|\Plumrocket\Token\Api\Data\CustomerInterface
     */
    protected function getTokenByDate(int $customerId, string $recipientEmail, string $createDate, string $typeKey)
    {
        $key = "$typeKey|$customerId|$recipientEmail|$createDate";

        if (! isset($this->localCache[$key])) {
            $sortOrder = $this->sortOrderBuilder->setDescendingDirection()->setField('customer_id')->create();

            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('customer_id', $customerId)
                ->addFilter('email', $recipientEmail)
                ->addFilter('create_at', $createDate)
                ->addFilter('type_key', $typeKey)
                ->addSortOrder($sortOrder)
                ->create();

            $tokenSearchResults = $this->tokenRepository->getList($searchCriteria);

            $this->localCache[$key] = $tokenSearchResults->getTotalCount()
                ? array_values($tokenSearchResults->getItems())[0]
                : false;
        }

        return $this->localCache[$key];
    }
}
