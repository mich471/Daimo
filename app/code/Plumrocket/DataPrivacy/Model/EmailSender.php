<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model;

use Exception;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Area;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use Plumrocket\DataPrivacy\Helper\Config\Email;
use Plumrocket\DataPrivacy\Model\Guest\GetPrivacyCenterUrl;
use Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterface;
use Plumrocket\Token\Api\GenerateForCustomerInterface;
use Psr\Log\LoggerInterface;

/**
 * @since 3.1.0
 */
class EmailSender extends DataObject
{
    /**
     * @var \Plumrocket\DataPrivacy\Helper\Config\Email
     */
    private $emailHelper;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Plumrocket\Token\Api\GenerateForCustomerInterface
     */
    private $tokenGenerator;

    /**
     * @var \Plumrocket\DataPrivacy\Model\Guest\GetPrivacyCenterUrl
     */
    private $getPrivacyCenterUrl;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    private $emulation;

    /**
     * @param \Plumrocket\DataPrivacy\Helper\Config\Email             $emailHelper
     * @param \Magento\Framework\Mail\Template\TransportBuilder       $transportBuilder
     * @param \Magento\Store\Model\StoreManagerInterface              $storeManager
     * @param \Psr\Log\LoggerInterface                                $logger
     * @param \Plumrocket\Token\Api\GenerateForCustomerInterface      $tokenGenerator
     * @param \Plumrocket\DataPrivacy\Model\Guest\GetPrivacyCenterUrl $getPrivacyCenterUrl
     * @param \Magento\Store\Model\App\Emulation                      $emulation
     * @param array                                                   $data
     */
    public function __construct(
        Email $emailHelper,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        GenerateForCustomerInterface $tokenGenerator,
        GetPrivacyCenterUrl $getPrivacyCenterUrl,
        Emulation $emulation,
        array $data = []
    ) {
        $this->emailHelper = $emailHelper;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->tokenGenerator = $tokenGenerator;
        parent::__construct($data);
        $this->getPrivacyCenterUrl = $getPrivacyCenterUrl;
        $this->emulation = $emulation;
    }

    /**
     * @return string
     */
    public function getSenderName(): string
    {
        return $this->emailHelper->getSenderName();
    }

    /**
     * @return string
     */
    public function getSenderEmail(): string
    {
        return $this->emailHelper->getSenderEmail();
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param null                                         $vars
     * @return bool
     */
    public function sendDownloadDataNotification(CustomerInterface $customer, $vars = null): bool
    {
        $this->checkCustomerBeforeSendNotification($customer);
        $template = $this->emailHelper->getDownloadConfirmationTemplate();

        return $this->sendNotification($template, $customer->getEmail(), $customer->getFirstname(), $vars);
    }

    /**
     * @param string $guestEmail
     * @param null   $vars
     * @return bool
     */
    public function sendGuestDownloadDataNotification(string $guestEmail, $vars = null): bool
    {
        $template = $this->emailHelper->getDownloadConfirmationTemplate();

        return $this->sendNotification($template, $guestEmail, null, $vars);
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param null                                         $vars
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendRemovalRequestNotification(CustomerInterface $customer,
        $vars = null): bool
    {
        $this->checkCustomerBeforeSendNotification($customer);
        $template = $this->emailHelper->getRemovalRequestTemplate();

        return $this->sendNotification($template, $customer->getEmail(), $customer->getFirstname(), $vars);
    }

    /**
     * Send email about removal request by admin.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface                $customer
     * @param \Plumrocket\DataPrivacyApi\Api\Data\RemovalRequestInterface $removalRequest
     * @param array                                                       $vars
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @since 3.2.0
     */
    public function sendAdminRemovalRequestNotification(
        CustomerInterface $customer,
        RemovalRequestInterface $removalRequest,
        array $vars = []
    ): bool {
        $this->checkCustomerBeforeSendNotification($customer);
        $vars['admin_comment'] = $removalRequest->getAdminComment();

        $this->emulation->startEnvironmentEmulation($customer->getStoreId());

        $result = $this->sendNotification(
            $this->emailHelper->getAdminRemovalRequestTemplate(),
            $customer->getEmail(),
            $customer->getFirstname(),
            $vars
        );

        $this->emulation->stopEnvironmentEmulation();

        return $result;
    }

    /**
     * @param string $guestEmail
     * @param null   $vars
     * @return bool
     */
    public function sendGuestRemovalRequestNotification(string $guestEmail, $vars = null): bool
    {
        $template = $this->emailHelper->getRemovalRequestTemplate();

        return $this->sendNotification($template, $guestEmail, null, $vars);
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function checkCustomerBeforeSendNotification(CustomerInterface $customer): EmailSender
    {
        if (! $customer->getEmail()) {
            throw new LocalizedException(__('Invalid customer.'));
        }

        return $this;
    }

    /**
     * @param      $template
     * @param null $vars
     * @return \Magento\Framework\Mail\Template\TransportBuilder
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getPreparedTransportBuilder($template, $vars = null): TransportBuilder
    {
        if (empty($vars) || ! is_array($vars)) {
            $vars = [];
        }

        return $this->transportBuilder->setTemplateIdentifier(
            $template
        )->setTemplateOptions(
            [
                'area'  => Area::AREA_FRONTEND,
                'store' => $this->storeManager->getStore()->getId(),
            ])->setTemplateVars(
            $vars
        )->setFrom(
            [
                'email' => $this->getSenderEmail(),
                'name'  => $this->getSenderName(),
            ]);
    }

    /**
     * @param      $template
     * @param      $toEmail
     * @param null $toName
     * @param null $vars
     * @return bool
     */
    private function sendNotification($template, $toEmail, $toName = null, $vars = null): bool
    {
        try {
            $toName = ! empty($toName) ? (string)$toName : 'Recipient Name';

            if (empty($toEmail)) {
                throw new LocalizedException(
                    __('Invalid specified recipient email address.')
                );
            }

            /* Send email */
            $this->getPreparedTransportBuilder($template, $vars)
                 ->addTo($toEmail, $toName)
                 ->getTransport()
                 ->sendMessage();

            return true;
        } catch (MailException | LocalizedException | Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return false;
    }

    /**
     * @param string $email
     * @param array  $vars
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\SecurityViolationException
     */
    public function sendGuestRequestEmail(string $email, array $vars = []): bool
    {
        $template = $this->emailHelper->getGuestTemplate();
        $token = $this->tokenGenerator->execute(
            0,
            $email,
            \Plumrocket\DataPrivacy\Model\Guest\PrivacyCenterToken::KEY
        );

        $vars['guestName'] = 'Guest';
        $vars['accessUrl'] = $this->getPrivacyCenterUrl->execute($token->getHash());
        $vars['expirationDate'] = $token->getExpireAt();

        return $this->sendNotification($template, $email, null, $vars);
    }
}
