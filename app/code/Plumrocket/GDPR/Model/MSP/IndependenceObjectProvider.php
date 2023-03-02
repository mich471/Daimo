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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Model\MSP;

class IndependenceObjectProvider
{
    /** @codingStandardsIgnoreFile */

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * IndependenceClassProvider constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Module\Manager         $moduleManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @return \MSP\ReCaptcha\Model\Config|false
     */
    public function getReCaptchaConfig()
    {
        return $this->get('\MSP\ReCaptcha\Model\Config');
    }

    /**
     * @return false|\MSP\ReCaptcha\Model\Provider\ResponseProviderInterface
     */
    public function getResponseProvider()
    {
        return $this->get('\MSP\ReCaptcha\Model\Provider\ResponseProviderInterface');
    }

    /**
     * @return false|\MSP\ReCaptcha\Api\ValidateInterface
     */
    public function getValidate()
    {
        return $this->get('\MSP\ReCaptcha\Api\ValidateInterface');
    }

    /**
     * @return false|\MSP\ReCaptcha\Model\Provider\Failure\AjaxResponseFailure
     */
    public function getFailureProvider()
    {
        return $this->get('\MSP\ReCaptcha\Model\Provider\Failure\AjaxResponseFailure');
    }

    /**
     * @param string $className
     * @return bool|mixed
     */
    private function get($className)
    {
        return $this->moduleManager->isEnabled('MSP_ReCaptcha')
            ? $this->objectManager->get($className)
            : false;
    }
}
