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
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Helper;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\AuthenticationInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Plumrocket\DataPrivacy\Model\Account\Data\Anonymizer;
use Plumrocket\DataPrivacy\Model\Account\Validator;

/**
 * Helper to get account specific data.
 *
 * @deprecated since 3.0.0
 * @see \Plumrocket\DataPrivacy\Model\Account\Validator
 * @see \Plumrocket\DataPrivacy\Model\Account\Data\Anonymizer
 */
class CustomerData extends AbstractHelper
{
    /**
     * @var AuthenticationInterface
     */
    protected $authentication;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var \Plumrocket\DataPrivacy\Model\Account\Data\Anonymizer
     */
    private $anonymizer;

    /**
     * @var \Plumrocket\DataPrivacy\Model\Account\Validator
     */
    private $validator;

    /**
     * @param \Magento\Framework\App\Helper\Context                      $context
     * @param \Magento\Customer\Model\AuthenticationInterface            $authentication
     * @param \Magento\Customer\Api\CustomerRepositoryInterface          $customerRepository
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Plumrocket\GDPR\Helper\Data                               $helper
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory        $customerFactory
     */
    public function __construct(
        Context $context,
        AuthenticationInterface $authentication,
        CustomerRepositoryInterface $customerRepository,
        OrderCollectionFactory $orderCollectionFactory,
        Data $helper,
        Anonymizer $anonymizer,
        Validator $validator
    ) {
        parent::__construct($context);
        $this->authentication = $authentication;
        $this->customerRepository = $customerRepository;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->helper = $helper;
        $this->anonymizer = $anonymizer;
        $this->validator = $validator;
    }

    /**
     * Check if customer has opened orders.
     *
     * @since 2.0.0
     * @param string $email
     * @return bool
     */
    public function hasGuestOpenedOrders(string $email): bool
    {
        return $this->validator->hasGuestOpenedOrders($email);
    }

    /**
     * Check if customer has opened orders.
     *
     * @since 2.0.0
     * @param int $customerId
     * @return bool
     */
    public function hasCustomerOpenedOrders(int $customerId): bool
    {
        return $this->validator->hasCustomerOpenedOrders($customerId);
    }

    /**
     * Check if customer has opened orders.
     *
     * @deprecated since 2.0.0
     * @see hasGuestOpenedOrders
     * @see hasCustomerOpenedOrders
     * @param $customerEmail
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function hasOpenedOrders($customerEmail)
    {
        return $this->validator->hasOpenedOrders($customerEmail);
    }

    /**
     * Authenticate user.
     *
     * @param CustomerInterface $currentCustomerDataObject
     * @param                   $password
     * @return void
     * @throws \Magento\Framework\Exception\InvalidEmailOrPasswordException
     * @throws \Magento\Framework\Exception\State\UserLockedException
     */
    public function authenticate(CustomerInterface $currentCustomerDataObject, $password)
    {
        try {
            $this->authentication->authenticate($currentCustomerDataObject->getId(), $password);
        } catch (InvalidEmailOrPasswordException $e) {
            throw new InvalidEmailOrPasswordException(__('The password you entered is incorrect. Please try again.'));
        }
    }

    /**
     * @param $customerId
     * @return string
     */
    public function getAnonymousString($customerId): string
    {
        return $this->anonymizer->getString($customerId);
    }

    /**
     * @param $customerId
     * @return string
     */
    public function getAnonymousEmail($customerId): string
    {
        return $this->anonymizer->getEmail($customerId);
    }

    /**
     * @param $data
     * @param int $customerId
     * @return array
     */
    public function getDataAnonymized($data, $customerId)
    {
        return $this->anonymizer->getData($data, $customerId);
    }
}
