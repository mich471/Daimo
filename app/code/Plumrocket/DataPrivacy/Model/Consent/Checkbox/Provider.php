<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model\Consent\Checkbox;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Plumrocket\DataPrivacyApi\Api\CheckboxRepositoryInterface;
use Plumrocket\DataPrivacyApi\Api\ConsentCheckboxProviderInterface;
use Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface;
use Plumrocket\DataPrivacyApi\Api\IsAlreadyCheckedCheckboxInterface;

class Provider implements ConsentCheckboxProviderInterface
{

    /**
     * @var \Plumrocket\DataPrivacyApi\Api\CheckboxRepositoryInterface
     */
    private $checkboxRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Plumrocket\DataPrivacyApi\Api\IsAlreadyCheckedCheckboxInterface
     */
    private $isAlreadyCheckedCheckbox;

    /**
     * @param \Plumrocket\DataPrivacyApi\Api\CheckboxRepositoryInterface       $checkboxRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder                     $searchCriteriaBuilder
     * @param \Plumrocket\DataPrivacyApi\Api\IsAlreadyCheckedCheckboxInterface $isAlreadyCheckedCheckbox
     */
    public function __construct(
        CheckboxRepositoryInterface $checkboxRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        IsAlreadyCheckedCheckboxInterface $isAlreadyCheckedCheckbox
    ) {
        $this->checkboxRepository = $checkboxRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->isAlreadyCheckedCheckbox = $isAlreadyCheckedCheckbox;
    }

    /**
     * @inheritdoc
     */
    public function getCheckboxesToAgreeByLocation(int $customerId, string $locationKey): array
    {
        $checkboxesToAgreeByLocation = [];
        foreach ($this->getEnabledCustomerCheckboxes($customerId) as $checkbox) {
            if (! $checkbox->isUsedInLocation($locationKey)
                || $this->isAlreadyCheckedCheckbox->execute($checkbox)
            ) {
                continue;
            }
            $checkboxesToAgreeByLocation[] = $checkbox;
        }
        return $checkboxesToAgreeByLocation;
    }

    /**
     * @inheritdoc
     */
    public function getCheckboxesToAgree(int $customerId): array
    {
        $checkboxesToAgree = [];
        foreach ($this->getEnabledCustomerCheckboxes($customerId) as $checkbox) {
            if ($this->isAlreadyCheckedCheckbox->execute($checkbox)) {
                continue;
            }
            $checkboxesToAgree[] = $checkbox;
        }
        return $checkboxesToAgree;
    }

    /**
     * @inheritdoc
     */
    public function getEnabledCustomerCheckboxes(int $customerId): array
    {
        $this->searchCriteriaBuilder->addFilter(CheckboxInterface::STATUS, 1);
        return $this->checkboxRepository->getList($this->searchCriteriaBuilder->create())->getItems();
    }
}
