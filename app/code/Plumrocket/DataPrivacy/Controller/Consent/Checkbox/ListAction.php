<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Controller\Consent\Checkbox;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Plumrocket\DataPrivacy\Helper\Config;
use Plumrocket\DataPrivacyApi\Api\ConsentCheckboxProviderInterface;
use Plumrocket\DataPrivacyApi\Api\ConvertConsentCheckboxToArrayInterface;

/**
 * @since 3.1.0
 */
class ListAction extends Action
{

    /**
     * @var \Plumrocket\DataPrivacyApi\Api\ConvertConsentCheckboxToArrayInterface
     */
    private $convertConsentCheckboxToArray;

    /**
     * @var \Plumrocket\DataPrivacyApi\Api\ConsentCheckboxProviderInterface
     */
    private $consentCheckboxProvider;

    /**
     * @var \Plumrocket\DataPrivacy\Helper\Config
     */
    private $config;

    /**
     * @param \Magento\Framework\App\Action\Context                                 $context
     * @param \Plumrocket\DataPrivacyApi\Api\ConvertConsentCheckboxToArrayInterface $convertConsentCheckboxToArray
     * @param \Plumrocket\DataPrivacyApi\Api\ConsentCheckboxProviderInterface       $consentCheckboxProvider
     * @param \Plumrocket\DataPrivacy\Helper\Config                                 $config
     */
    public function __construct(
        Context $context,
        ConvertConsentCheckboxToArrayInterface $convertConsentCheckboxToArray,
        ConsentCheckboxProviderInterface $consentCheckboxProvider,
        Config $config
    ) {
        parent::__construct($context);
        $this->convertConsentCheckboxToArray = $convertConsentCheckboxToArray;
        $this->consentCheckboxProvider = $consentCheckboxProvider;
        $this->config = $config;
    }

    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        if (! $this->config->isModuleEnabled()) {
            return $resultJson->setHttpResponseCode(400)->setData(
                ['message' => __('Module Data Privacy is disabled, please contact site administrator.')]
            );
        }

        $locationKey = $this->getRequest()->getParam('locationKey', '');
        if ($locationKey) {
            $checkboxes = $this->consentCheckboxProvider->getCheckboxesToAgreeByLocation(0, $locationKey);
        } else {
            $checkboxes = $this->consentCheckboxProvider->getCheckboxesToAgree(0);
        }

        $consentsAsArray = [];
        foreach ($checkboxes as $checkbox) {
            $consentsAsArray[] = $this->convertConsentCheckboxToArray->execute($checkbox);
        }

        return $resultJson->setData($consentsAsArray);
    }
}
