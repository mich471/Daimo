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

declare(strict_types=1);

namespace Plumrocket\GDPR\Controller\Consentcheckboxes;

use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;

class Update extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Plumrocket\GDPR\Helper\Checkboxes
     */
    private $checkboxHelper;

    /**
     * Update constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Plumrocket\GDPR\Helper\Checkboxes $checkboxHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Plumrocket\GDPR\Helper\Checkboxes $checkboxHelper
    ) {
        parent::__construct($context);
        $this->checkboxHelper = $checkboxHelper;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        $checkboxRequest = $this->getRequest()->getParam('prgdpr_consent', []);
        $checkboxesToRefuse = [];
        $checkboxesToAccept = [];

        try {
            $checkboxes = $this->checkboxHelper->getCheckboxes(ConsentLocations::MY_ACCOUNT, true, true, true);
            foreach ($checkboxes as $checkbox) {
                /** @var \Plumrocket\GDPR\Api\Data\CheckboxInterface $checkboxChecked */
                $checkboxChecked = $checkbox->isAlreadyChecked();
                $checkboxVersion = $checkbox->getCmsPageInfo('version') ?: 0;
                $issetCheckboxInRequest
                    = isset($checkboxRequest[$checkbox->getId()][$checkboxVersion]);

                if ($checkboxChecked && ! $issetCheckboxInRequest) {
                    if (! $checkbox->canDecline()) {
                        continue;
                    }
                    $checkboxesToRefuse[] = $checkbox;
                } elseif (! $checkboxChecked && $issetCheckboxInRequest) {
                    $checkboxesToAccept[] = $checkbox;
                }
            }

            $this->checkboxHelper->saveConsents(ConsentLocations::MY_ACCOUNT, $checkboxesToAccept);
            $this->checkboxHelper->saveConsents(
                ConsentLocations::MY_ACCOUNT,
                $checkboxesToRefuse,
                ['action' => 0]
            );
            $this->messageManager->addSuccessMessage(
                __('%1 consent(s) was updated', [count($checkboxesToRefuse) + count($checkboxesToAccept)])
            );
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $this->messageManager->addErrorMessage(__($exception->getMessage()));
        }

        return $result->setData([]);
    }
}
