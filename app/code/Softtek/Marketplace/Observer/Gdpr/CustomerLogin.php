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

namespace Softtek\Marketplace\Observer\Gdpr;

use Plumrocket\GDPR\Observer\CustomerLogin as GdprCustomerLogin;
use Plumrocket\GDPR\Helper\Data;
use Plumrocket\GDPR\Helper\Checkboxes;
use Magento\Framework\Message\ManagerInterface;
use Plumrocket\GDPR\Model\ResourceModel\RemovalRequestsFactory;
use Plumrocket\GDPR\Model\ResourceModel\RemovalRequests\CollectionFactory;
use Plumrocket\GDPR\Model\Config\Source\RemovalStatus;
use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;

class CustomerLogin extends GdprCustomerLogin
{
    /**
     * @var RemovalRequestsFactory
     */
    private $removalResourceFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * @var Checkboxes
     */
    private $checkboxesHelper;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * CustomerLogin constructor.
     *
     * @param Data                                           $dataHelper
     * @param Checkboxes                                     $checkboxesHelper
     * @param ManagerInterface                            $messageManager
     * @param RemovalRequestsFactory            $removalResourceFactory
     * @param CollectionFactory $collectionFactory
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig,
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     */
    public function __construct(
        Data $dataHelper,
        Checkboxes $checkboxesHelper,
        ManagerInterface $messageManager,
        RemovalRequestsFactory $removalResourceFactory,
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation
    ) {
        $this->dataHelper = $dataHelper;
        $this->checkboxesHelper = $checkboxesHelper;
        $this->messageManager = $messageManager;
        $this->removalResourceFactory = $removalResourceFactory;
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;

        parent::__construct($dataHelper, $checkboxesHelper, $messageManager, $removalResourceFactory, $collectionFactory);
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
                $this->sendReactivationNotification($customer);
            }

            $this->messageManager->addSuccessMessage(
                __("Parabéns! Você reativou sua conta.")
            );
        }

        return $this;
    }

    /**
     * @param $customer
     * @return $this
     */
    protected function sendReactivationNotification($customer)
    {
        try {
            $templateOptions = array('area' => Area::AREA_FRONTEND, 'store' => $this->storeManager->getStore()->getId());
            $templateVars = array(
                'store' => $this->storeManager->getStore(),
                'customer_name' => $customer->getFirstname()
            );

            $senderEmail = $this->scopeConfig->getValue('trans_email/ident_general/email',ScopeInterface::SCOPE_STORE);
            $senderName = $this->scopeConfig->getValue('trans_email/ident_general/name',ScopeInterface::SCOPE_STORE);
            $from = ['email' => $senderEmail, 'name' => $senderName];
            $this->inlineTranslation->suspend();
            $to = [$customer->getEmail()];

            $templateId = $this->scopeConfig->getValue ( 'purpletree_marketplace/general/reactivation_email_template', ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()->getId() );
            $transport = $this->transportBuilder->setTemplateIdentifier($templateId)->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($to)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();

            $this->messageManager->addSuccessMessage(__('Your account has been reactivated'));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Error sending reactivation customer email notification'));
        }
    }
}
