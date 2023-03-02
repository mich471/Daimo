<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model;

use Magento\Eav\Model\Entity\Attribute\Exception as AttributeException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\ValidatorException;
use Plumrocket\DataPrivacyApi\Api\CheckboxRepositoryInterface;
use Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface as DataCheckboxInterface;
use Plumrocket\DataPrivacyApi\Api\Data\CheckboxSearchResultsInterface;

class CheckboxRepository implements CheckboxRepositoryInterface
{
    /**
     * @var DataCheckboxInterface[]
     */
    protected $instancesById = []; //@codingStandardsIgnoreLine

    /**
     * @var \Plumrocket\GDPR\Model\CheckboxFactory
     */
    private $checkboxFactory;

    /**
     * @var ResourceModel\Checkbox
     */
    private $resourceModel;

    /**
     * @var ResourceModel\Checkbox\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Plumrocket\GDPR\Api\Data\CheckboxSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * ProductRepository constructor.
     *
     * @param \Plumrocket\GDPR\Model\CheckboxFactory                          $checkboxFactory
     * @param \Plumrocket\GDPR\Model\ResourceModel\Checkbox                   $resourceModel
     * @param \Plumrocket\GDPR\Model\ResourceModel\Checkbox\CollectionFactory $collectionFactory
     * @param \Plumrocket\GDPR\Api\Data\CheckboxSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        \Plumrocket\GDPR\Model\CheckboxFactory $checkboxFactory,
        \Plumrocket\GDPR\Model\ResourceModel\Checkbox $resourceModel,
        \Plumrocket\GDPR\Model\ResourceModel\Checkbox\CollectionFactory $collectionFactory,
        \Plumrocket\GDPR\Api\Data\CheckboxSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->checkboxFactory = $checkboxFactory;
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * Create product
     *
     * @param DataCheckboxInterface $checkbox
     * @return DataCheckboxInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(DataCheckboxInterface $checkbox) : DataCheckboxInterface
    {
        try {
            unset($this->instancesById[$checkbox->getId()]);
            $this->resourceModel->save($checkbox);
        } catch (AttributeException $exception) {
            throw InputException::invalidFieldValue(
                $exception->getAttributeCode(),
                $checkbox->getData($exception->getAttributeCode()),
                $exception
            );
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __('The checkbox was unable to be saved. Please try again.'),
                $e
            );
        }

        unset($this->instancesById[$checkbox->getId()]);

        return $this->getById($checkbox->getId(), $checkbox->getStoreId());
    }

    /**
     * Get info about checkbox by checkbox id
     *
     * @param int      $checkboxId
     * @param int|null $storeId
     * @param bool     $forceReload
     * @return DataCheckboxInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($checkboxId, $storeId = null, $forceReload = false) : DataCheckboxInterface
    {
        if (! isset($this->instancesById[$checkboxId][$storeId]) || $forceReload) {
            /** @var \Plumrocket\GDPR\Model\Checkbox $checkbox */
            $checkbox = $this->checkboxFactory->create();
            if ($storeId !== null) {
                $checkbox->setStoreId($storeId);
            }
            $checkbox->load($checkboxId);
            if (! $checkbox->getId()) {
                throw new NoSuchEntityException(
                    __("The checkbox that was requested doesn't exist. Verify the checkbox and try again.")
                );
            }

            $this->instancesById[$checkboxId][$storeId] = $checkbox;
        }

        return $this->instancesById[$checkboxId][$storeId];
    }

    /**
     * Delete checkbox
     *
     * @param DataCheckboxInterface $checkbox
     * @return bool
     * @throws CouldNotSaveException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function delete(DataCheckboxInterface $checkbox) : bool
    {
        $checkboxId = $checkbox->getId();
        try {
            unset($this->instancesById[$checkboxId]);
            $this->resourceModel->delete($checkbox);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __('The "%1" checkbox couldn\'t be removed.', $checkboxId)
            );
        }

        unset($this->instancesById[$checkboxId]);

        return true;
    }

    /**
     * @param int $id
     * @return bool Will returned True if deleted
     * @throws CouldNotSaveException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function deleteById($id) : bool
    {
        $checkbox = $this->getById($id);
        return $this->delete($checkbox);
    }

    /**
     * Get checkbox list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return CheckboxSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Plumrocket\GDPR\Model\ResourceModel\Checkbox\Collection $collection */
        $collection = $this->collectionFactory->create();

        $collection->addAttributeToSelect('*');

        foreach ($searchCriteria->getFilterGroups() as $group) {
            foreach ($group->getFilters() as $filter) {
                $conditionType = $filter->getConditionType() ?: 'eq';

                $collection->addFieldToFilter($filter->getField(), [$conditionType => $filter->getValue()]);
            }
        }

        /** @var SortOrder $sortOrder */
        foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
            $field = $sortOrder->getField();
            $collection->addOrder(
                $field,
                ($sortOrder->getDirection() === SortOrder::SORT_ASC)? SortOrder::SORT_ASC : SortOrder::SORT_DESC
            );
        }

        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /** @var CheckboxSearchResultsInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }
}
