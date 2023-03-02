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

declare(strict_types=1);

namespace Plumrocket\GDPR\Block\Account;

use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\View\Element\Template\Context;
use Plumrocket\GDPR\Block\GDPR;
use Plumrocket\GDPR\Helper\CustomerData;

/**
 * Customer gdpr account block.
 * @deprecated since 3.1.0
 * @see \Plumrocket\DataPrivacy\ViewModel\Dashboard\Export
 */
class Export extends GDPR
{

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private $httpContext;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Plumrocket\GDPR\Helper\CustomerData             $customerData
     * @param \Magento\Framework\App\Http\Context              $httpContext
     * @param array                                            $data
     */
    public function __construct(
        Context         $context,
        CustomerData    $customerData,
        HttpContext     $httpContext,
        array $data = []
    ) {
        parent::__construct($context, $customerData, $data);
        $this->httpContext = $httpContext;
    }

    /**
     * @param array $params
     * @return string
     */
    public function getExportAction($params = [])
    {
        $isLoggedIn = $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        if ($isLoggedIn) {
            return $this->getUrl('prgdpr/customer/export', $params);
        }

        return $this->getUrl('prgdpr/guest/export', $params);
    }

    /**
     * @param array $params
     * @return string
     */
    public function goBackUrl($params = [])
    {
        return $this->getUrl('prgdpr/account/index', $params);
    }

    /**
     * Get forgot password page url.
     *
     * @return string
     */
    public function getForgotPasswordPageUrl()
    {
        return $this->getUrl('customer/account/forgotpassword');
    }

    /**
     * @return mixed
     */
    public function getSecureToken()
    {
        return $this->getRequest()->getParam('token');
    }
}
