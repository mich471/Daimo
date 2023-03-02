<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\ViewModel;

use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\UrlInterface;
use Plumrocket\DataPrivacyApi\Api\ConvertConsentCheckboxToArrayInterface;
use Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface;

class ConvertConsentCheckbox implements ConvertConsentCheckboxToArrayInterface
{

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    private $filterProvider;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Framework\UrlInterface            $urlBuilder
     */
    public function __construct(FilterProvider $filterProvider, UrlInterface $urlBuilder)
    {
        $this->filterProvider = $filterProvider;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritDoc
     */
    public function execute(CheckboxInterface $checkbox): array
    {
        $policyData = null;
        if ($policy = $checkbox->getPolicy()) {
            $policyData = [
                'title'   => $policy->getTitle(),
                'url'     => $this->urlBuilder->getUrl($policy->getUrlKey(), ['_secure' => true]),
                'content' => $this->filterProvider->getPageFilter()->filter($policy->getContent()),
                'version' => $policy->getVersion(),
            ];
        }

        return [
            'consentId'     => $checkbox->getId(),
            'checkboxLabel' => $checkbox->getLabel(),
            'is_required'   => $checkbox->isRequiredForValidate(),
            'policy'        => $policyData,
            'page_id'       => $checkbox->getCmsPageId(),
            'canDecline'    => $checkbox->canDecline(),
        ];
    }
}
