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

namespace Plumrocket\GDPR\Block\Account;

use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;

/**
 * Customer gdpr account block.
 * @deprecated since 3.0.0
 * @see \Plumrocket\DataPrivacy\ViewModel\Dashboard\Navigation
 */
class Index extends \Plumrocket\GDPR\Block\GDPR
{
    /**
     * @var \Plumrocket\GDPR\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Plumrocket\DataPrivacyApi\Api\ConsentCheckboxProviderInterface
     */
    protected $checkboxProvider;

    /**
     * @param \Magento\Framework\View\Element\Template\Context                $context
     * @param \Plumrocket\GDPR\Helper\CustomerData                            $customerData
     * @param \Plumrocket\GDPR\Helper\Data                                    $dataHelper
     * @param \Plumrocket\DataPrivacyApi\Api\ConsentCheckboxProviderInterface $checkboxProvider
     * @param array                                                           $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Plumrocket\GDPR\Helper\CustomerData $customerData,
        \Plumrocket\GDPR\Helper\Data $dataHelper,
        \Plumrocket\DataPrivacyApi\Api\ConsentCheckboxProviderInterface $checkboxProvider,
        array $data = []
    ) {
        parent::__construct($context, $customerData, $data);
        $this->dataHelper = $dataHelper;
        $this->checkboxProvider = $checkboxProvider;
    }

    /**
     * Get export page url.
     *
     * @return string
     */
    public function getExportPageUrl()
    {
        return $this->getUrl('prgdpr/account/export', $this->getUrlParams());
    }

    /**
     * Get delete page url.
     *
     * @return string
     */
    public function getDeletingPageUrl()
    {
        return $this->getUrl('prgdpr/account/delete', $this->getUrlParams());
    }

    /**
     * @return array
     */
    public function getUrlParams()
    {
        $token = $this->getToken();

        return $token ? ['token' => $token] : [];
    }

    /**
     * @return bool
     */
    public function showMyConsentsPage(): bool
    {
        return ! empty($this->checkboxProvider->getEnabledCustomerCheckboxes(ConsentLocations::MY_ACCOUNT));
    }
}
