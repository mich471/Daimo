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
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Observer;

use Magento\Framework\Event\ObserverInterface;
use Plumrocket\GDPR\Model\Config\Source\RemovalStatus;

class CustomerLogin implements ObserverInterface
{
    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\RemovalRequestsFactory
     */
    private $removalResourceFactory;

    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\RemovalRequests\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Plumrocket\GDPR\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Plumrocket\GDPR\Helper\Checkboxes
     */
    private $checkboxesHelper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * CustomerLogin constructor.
     *
     * @param \Plumrocket\GDPR\Helper\Data                                           $dataHelper
     * @param \Plumrocket\GDPR\Helper\Checkboxes                                     $checkboxesHelper
     * @param \Magento\Framework\Message\ManagerInterface                            $messageManager
     * @param \Plumrocket\GDPR\Model\ResourceModel\RemovalRequestsFactory            $removalResourceFactory
     * @param \Plumrocket\GDPR\Model\ResourceModel\RemovalRequests\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Plumrocket\GDPR\Helper\Data $dataHelper,
        \Plumrocket\GDPR\Helper\Checkboxes $checkboxesHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Plumrocket\GDPR\Model\ResourceModel\RemovalRequestsFactory $removalResourceFactory,
        \Plumrocket\GDPR\Model\ResourceModel\RemovalRequests\CollectionFactory $collectionFactory
    ) {
        $this->dataHelper = $dataHelper;
        $this->checkboxesHelper = $checkboxesHelper;
        $this->messageManager = $messageManager;
        $this->removalResourceFactory = $removalResourceFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $observer->getData('customer');

        if ($customer && $this->dataHelper->moduleEnabled()) {
            $this->cancelAllRemovalRequests($customer);
        }
    }

    /**
     * @param $customer
     * @return $this
     */
    private function cancelAllRemovalRequests($customer)
    {
        if (! $customer || ! $customer->getId()) {
            return $this;
        }

        /** @var \Plumrocket\GDPR\Model\ResourceModel\RemovalRequests\Collection $removalRequests */
        $removalRequests = $this->collectionFactory->create()
            ->addFieldToFilter('customer_email', ['eq' => $customer->getEmail()])
            ->addFieldToFilter('status', ['eq' =>  RemovalStatus::PENDING]);

        if ($removalRequests->getSize()) {
            foreach ($removalRequests->getItems() as $removalRequest) {
                /** @var \Plumrocket\GDPR\Model\RemovalRequests $removalRequest */
                $removalRequest->addData([
                    'cancelled_at' => $this->checkboxesHelper->getFormattedGmtDateTime(),
                    'cancelled_by' => 'Customer',
                    'scheduled_at' => null,
                    'status' => RemovalStatus::CANCELLED
                ]);
                /** @var \Plumrocket\GDPR\Model\ResourceModel\RemovalRequests $removalRequestResource */
                $removalRequestResource = $this->removalResourceFactory->create();
                $removalRequestResource->save($removalRequest);
            }

            $this->messageManager->addSuccessMessage(
                __("Parabéns! Você reativou sua conta.")
            );
        }

        return $this;
    }
}
