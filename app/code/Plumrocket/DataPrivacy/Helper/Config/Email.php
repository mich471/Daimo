<?php
/**
 * @package     Plumrocket_magento2.3.6
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Helper\Config;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Plumrocket\Base\Model\Utils\Config;

/**
 * @since 3.1.0
 */
class Email extends AbstractHelper
{
    public const XML_PATH_PROTECTION_OFFICER = 'prgdpr/dashboard/protection_officer_email';
    public const XML_PATH_SENDER_NAME = 'prgdpr/email/sender_name';
    public const XML_PATH_SENDER_EMAIL = 'prgdpr/email/sender_email';
    public const XML_PATH_DOWNLOAD_CONFIRM_TEMPLATE = 'prgdpr/email/download_confirmation_template';
    public const XML_PATH_REMOVAL_REQUEST_TEMPLATE = 'prgdpr/email/removal_request_template';
    public const XML_PATH_ADMIN_REMOVAL_REQUEST_TEMPLATE = 'prgdpr/email/admin_removal_request_template';
    public const XML_PATH_GUEST_TEMPLATE = 'prgdpr/email/guest_email_template';

    /**
     * @var \Plumrocket\Base\Model\Utils\Config
     */
    private $configUtils;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Plumrocket\Base\Model\Utils\Config   $configUtils
     */
    public function __construct(
        Context $context,
        Config $configUtils
    ) {
        parent::__construct($context);

        $this->configUtils = $configUtils;
    }

    /**
     * @return string
     */
    public function getProtectionOfficer(): string
    {
        return (string) $this->configUtils->getConfig(self::XML_PATH_PROTECTION_OFFICER);
    }

    /**
     * @return string
     */
    public function getSenderName(): string
    {
        return (string) $this->configUtils->getConfig(self::XML_PATH_SENDER_NAME);
    }

    /**
     * @return string
     */
    public function getSenderEmail(): string
    {
        return (string) $this->configUtils->getConfig(self::XML_PATH_SENDER_EMAIL);
    }

    /**
     * @return string
     */
    public function getDownloadConfirmationTemplate(): string
    {
        return (string) $this->configUtils->getConfig(self::XML_PATH_DOWNLOAD_CONFIRM_TEMPLATE);
    }

    /**
     * @return string
     */
    public function getRemovalRequestTemplate(): string
    {
        return (string) $this->configUtils->getConfig(self::XML_PATH_REMOVAL_REQUEST_TEMPLATE);
    }

    /**
     * Get email template for removal request notification.
     *
     * @return string
     * @since 3.2.0
     */
    public function getAdminRemovalRequestTemplate(): string
    {
        return (string) $this->configUtils->getConfig(self::XML_PATH_ADMIN_REMOVAL_REQUEST_TEMPLATE);
    }

    /**
     * @return string
     */
    public function getGuestTemplate(): string
    {
        return (string) $this->configUtils->getConfig(self::XML_PATH_GUEST_TEMPLATE);
    }
}
