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

namespace Plumrocket\GDPR\Model\Checkbox;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Plumrocket\DataPrivacyApi\Api\CheckboxProviderInterface;
use Plumrocket\DataPrivacyApi\Api\CheckboxRepositoryInterface;
use Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface;
use Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterfaceFactory;
use Plumrocket\GDPR\Helper\Geo\Location;
use Plumrocket\GDPR\Model\ResourceModel\ConsentsLog\CollectionFactory;

class Provider implements CheckboxProviderInterface
{

    /**
     * @var array[]
     */
    private $checkboxes;

    /**
     * @var array[]
     */
    private $enabledCheckboxes;

    /**
     * @var \Plumrocket\DataPrivacyApi\Api\CheckboxRepositoryInterface
     */
    private $checkboxRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\ConsentsLog\CollectionFactory
     */
    private $consentLogCollectionFactory;

    /**
     * @var \Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterfaceFactory
     */
    private $checkboxFactory;

    /**
     * @var \Plumrocket\GDPR\Helper\Geo\Location
     */
    private $geoLocationHelper;

    /**
     * Provider constructor.
     *
     * @param \Plumrocket\DataPrivacyApi\Api\CheckboxRepositoryInterface $checkboxRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Plumrocket\GDPR\Model\ResourceModel\ConsentsLog\CollectionFactory $consentLogCollectionFactory
     * @param \Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterfaceFactory $checkboxFactory
     * @param \Plumrocket\GDPR\Helper\Geo\Location $geoLocationHelper
     */
    public function __construct(
        CheckboxRepositoryInterface $checkboxRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CollectionFactory $consentLogCollectionFactory,
        CheckboxInterfaceFactory $checkboxFactory,
        Location $geoLocationHelper
    ) {
        $this->checkboxRepository = $checkboxRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->consentLogCollectionFactory = $consentLogCollectionFactory;
        $this->checkboxFactory = $checkboxFactory;
        $this->geoLocationHelper = $geoLocationHelper;
    }

    /**
     * Load only enabled checkboxes
     *
     * @param bool $forceReload
     * @return CheckboxInterface[]
     */
    public function getEnabled($forceReload = false): array
    {
        if (null === $this->enabledCheckboxes || $forceReload) {
            $this->searchCriteriaBuilder->addFilter(
                CheckboxInterface::STATUS,
                1
            );

            $searchCriteria = $this->searchCriteriaBuilder->create();

            $this->enabledCheckboxes = $this->checkboxRepository->getList($searchCriteria)->getItems();
        }

        return $this->enabledCheckboxes;
    }

    /**
     * Load all checkboxes
     *
     * @param bool $forceReload
     * @return CheckboxInterface[]
     */
    public function getAll($forceReload = false): array
    {
        if (null === $this->checkboxes || $forceReload) {
            $searchCriteria = $this->searchCriteriaBuilder->create();

            $this->checkboxes = $this->checkboxRepository->getList($searchCriteria)->getItems();
        }

        return $this->checkboxes;
    }

    /**
     * @param string $locationKey
     * @return CheckboxInterface[]
     */
    public function getByLocation($locationKey): array
    {
        $checkboxes = [];

        if ($locationKey) {
            foreach ($this->getAll() as $checkbox) {
                if (in_array($locationKey, $checkbox->getLocationKeys())) {
                    $checkboxes[$checkbox->getId()] = $checkbox;
                }
            }
        }

        return $checkboxes;
    }

    /**
     * Retrieve all enabled checkboxes and including checkboxes with old version of document which is accepted
     *
     * @param int $customerId
     * @return CheckboxInterface[]
     */
    public function getEnabledWithOldChecked(int $customerId)
    {
        /** @var \Plumrocket\GDPR\Model\ResourceModel\ConsentsLog\Collection $consents */
        $consents = $this->consentLogCollectionFactory->create();

        $consents
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('checkbox_id', ['in' => $this->getAllIds($this->getEnabled())]);

        $consents->getSelect()
                 ->reset(\Zend_Db_Select::COLUMNS)
                 ->group(['checkbox_id', 'version', 'customer_id']);
        $consents->addExpressionFieldToSelect('consents', 'MAX({{consent_id}})', ['consent_id' => 'consent_id']);

        $consents = $this->consentLogCollectionFactory->create()
                                                      ->addExpressionFieldToSelect(
                                                          'max_version', 'MAX({{version}})', ['version' => 'version']
                                                      )
                                                      ->addFieldToFilter('consent_id', ['in' => $consents->getSelect()])
                                                      ->addFieldToFilter('action', 1);

        $consents->getSelect()->group(['checkbox_id']);
        $enabledConsents = $this->getEnabled();
        $enabledConsents = array_filter($enabledConsents, [$this->geoLocationHelper, 'isPassCheckboxGeoIPRestriction']);

        foreach ($consents as $key => $consent) {
            $enabledConsent = $enabledConsents[$consent->getCheckboxId()] ?? null;

            if ($enabledConsent
                && $this->geoLocationHelper->isPassCheckboxGeoIPRestriction($enabledConsent)
                && null !== $consent->getMaxVersion()
                && (float) $consent->getMaxVersion() !== (float) $enabledConsent->getCmsPageInfo('version')
                && ! $enabledConsent->isAlreadyChecked()
            ) {
                $oldCheckbox = $this->checkboxFactory->create()->addData($consent->getData());
                $oldCheckbox->addData([
                                          CheckboxInterface::LABEL => $enabledConsent->getLabel(),
                                          'entity_id'              => $enabledConsent->getId(),
                                          'action'                 => 1,
                                          'version'                => $consent->getMaxVersion(),
                                      ]);

                $enabledConsents[] = $oldCheckbox;
            }
        }

        return $this->sortItems($enabledConsents);
    }

    /**
     * @param array $items
     * @return array
     */
    private function getAllIds(array $items): array
    {
        $ids = [];
        foreach ($items as $item) {
            $ids[] = $item->getId();
        }
        return $ids;
    }

    /**
     * @param CheckboxInterface[] $items
     * @return CheckboxInterface[]
     */
    private function sortItems(array $items): array
    {
        usort($items, static function ($prevItem, $currentItem) {
            /**
             * @var CheckboxInterface $prevItem
             * @var CheckboxInterface $currentItem
             */
            if ($prevItem->getId() === $currentItem->getId()) {
                return $prevItem->getCmsPageInfo('version') <=> $currentItem->getCmsPageInfo('version');
            }

            return $prevItem->getId() <=> $currentItem->getId();
        });

        return $items;
    }
}
