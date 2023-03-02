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
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\CookieConsent\Controller\Consent\Guest;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Plumrocket\CookieConsent\Helper\Config;
use Plumrocket\CookieConsent\Model\Consent\CreateSettingsFromParam;

/**
 * Log consent and clear rejected cookies
 */
class Update extends Action
{
    /**
     * @var \Plumrocket\CookieConsent\Helper\Config
     */
    private $config;

    /**
     * @var \Plumrocket\CookieConsent\Model\Consent\CreateSettingsFromParam
     */
    private $createSettingsFromParam;

    /**
     * @param \Magento\Framework\App\Action\Context                           $context
     * @param \Plumrocket\CookieConsent\Helper\Config                         $config
     * @param \Plumrocket\CookieConsent\Model\Consent\CreateSettingsFromParam $createSettingsFromParam
     */
    public function __construct(
        Context $context,
        Config $config,
        CreateSettingsFromParam $createSettingsFromParam
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->createSettingsFromParam = $createSettingsFromParam;
    }

    /**
     * Execute controller.
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        if ($this->config->isModuleEnabled()) {

            $acceptedKeys = (array) $this->getRequest()->getParam('acceptedKeys');

            $settings = $this->createSettingsFromParam->execute($acceptedKeys);

            $this->_eventManager->dispatch(
                'pr_cookie_consent_update',
                ['settings' => $settings]
            );

            $response = ['Update processed.'];
        } else {
            $resultJson->setHttpResponseCode(404);
            $response = ['message' => __('Not found.')];
        }

        return $resultJson->setData($response);
    }
}
