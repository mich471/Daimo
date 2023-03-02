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

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Model;

use Magento\Eav\Model\Entity\Attribute\Exception as AttributeException;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Plumrocket\CookieConsent\Api\CategoryRepositoryInterface;
use Plumrocket\CookieConsent\Api\Data\CategoryInterface;
use Plumrocket\CookieConsent\Api\Data\CategoryInterfaceFactory;
use Plumrocket\CookieConsent\Api\Data\CategorySearchResultsInterfaceFactory;
use Plumrocket\CookieConsent\Api\GetCategoryIdByKeyInterface;
use Plumrocket\CookieConsent\Model\ResourceModel\Category\CollectionFactory;

/**
 * @since 1.0.0
 */
class CategoryRepository implements CategoryRepositoryInterface
{
    private $instancesById = [];
    private $instancesByKey = [];

    /**
     * @var \Plumrocket\CookieConsent\Api\Data\CategoryInterfaceFactory
     */
    private $categoryFactory;

    /**
     * @var \Plumrocket\CookieConsent\Model\ResourceModel\Category
     */
    private $resourceModel;

    /**
     * @var \Plumrocket\CookieConsent\Api\GetCategoryIdByKeyInterface
     */
    private $getCategoryIdByKey;

    /**
     * @var \Plumrocket\CookieConsent\Model\ResourceModel\Category\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Plumrocket\CookieConsent\Api\Data\CategorySearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * CategoryRepository constructor.
     *
     * @param \Plumrocket\CookieConsent\Api\Data\CategoryInterfaceFactory              $categoryFactory
     * @param \Plumrocket\CookieConsent\Model\ResourceModel\Category                   $resourceModel
     * @param \Plumrocket\CookieConsent\Api\GetCategoryIdByKeyInterface                $getCategoryIdByKey
     * @param \Plumrocket\CookieConsent\Model\ResourceModel\Category\CollectionFactory $collectionFactory
     * @param \Plumrocket\CookieConsent\Api\Data\CategorySearchResultsInterfaceFactory $searchResultsFactory
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface       $collectionProcessor
     */
    public function __construct(
        CategoryInterfaceFactory $categoryFactory,
        \Plumrocket\CookieConsent\Model\ResourceModel\Category $resourceModel,
        GetCategoryIdByKeyInterface $getCategoryIdByKey,
        CollectionFactory $collectionFactory,
        CategorySearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->resourceModel = $resourceModel;
        $this->getCategoryIdByKey = $getCategoryIdByKey;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(CategoryInterface $category): CategoryInterface
    {
        try {
            $this->clearLocalCache($category);
            $this->resourceModel->save($category);
        } catch (AttributeException $exception) {
            throw InputException::invalidFieldValue(
                $exception->getAttributeCode(),
                $category->getData($exception->getAttributeCode()),
                $exception
            );
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __('The cookie category was unable to be saved. Please try again.'),
                $e
            );
        }

        $this->clearLocalCache($category);

        return $this->getById($category->getId(), $category->getStoreId());
    }

    /**
     * Get info about checkbox by checkbox id
     *
     * @param int      $categoryId
     * @param int|null $storeId
     * @param bool     $forceReload
     * @return CategoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $categoryId, int $storeId = null, bool $forceReload = false) : CategoryInterface
    {
        if (! isset($this->instancesById[$categoryId][$storeId]) || $forceReload) {
            /** @var \Plumrocket\CookieConsent\Model\Category $category */
            $category = $this->categoryFactory->create();
            if ($storeId !== null) {
                $category->setStoreId($storeId);
            }
            $category->load($categoryId);
            if (! $category->getId()) {
                throw new NoSuchEntityException(
                    __("The category that was requested doesn't exist. Verify the cookie category and try again.")
                );
            }

            $this->saveToLocalCache($category, $storeId);
        }

        return $this->instancesById[$categoryId][$storeId];
    }

    /**
     * @inheritDoc
     */
    public function get(string $categoryKey, int $storeId = null, bool $forceReload = false): CategoryInterface
    {
        if (! isset($this->instancesByKey[$categoryKey][$storeId]) || $forceReload) {
            $categoryId = $this->getCategoryIdByKey->execute($categoryKey, $forceReload);
            if (! $categoryId) {
                throw new NoSuchEntityException(
                    __("The category that was requested doesn't exist. Verify the cookie category and try again.")
                );
            }

            return $this->getById($categoryId);
        }

        return $this->instancesByKey[$categoryKey][$storeId];
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        /** @var \Plumrocket\CookieConsent\Model\ResourceModel\Category\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect('*');
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var \Plumrocket\CookieConsent\Api\Data\CategorySearchResultsInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    /**
     * @inheritDoc
     */
    public function delete(CategoryInterface $category): bool
    {
        $categoryId = $category->getId();
        $categoryKey = $category->getKey();
        try {
            $this->clearLocalCache($category);
            $this->resourceModel->delete($category);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('The "%1" category couldn\'t be removed.', $categoryId)
            );
        }

        unset($this->instancesById[$categoryId], $this->instancesByKey[$categoryKey]);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $id): bool
    {
        $category = $this->getById($id);
        return $this->delete($category);
    }

    /**
     * @param \Plumrocket\CookieConsent\Api\Data\CategoryInterface $category
     * @param int|null                                             $storeId
     * @return $this
     */
    private function saveToLocalCache(CategoryInterface $category, int $storeId = null)
    {
        $this->instancesById[$category->getId()][$storeId] = $category;
        $this->instancesByKey[$category->getKey()][$storeId] = $category;
        return $this;
    }

    /**
     * @param \Plumrocket\CookieConsent\Api\Data\CategoryInterface $category
     * @return $this
     */
    private function clearLocalCache(CategoryInterface $category)
    {
        unset($this->instancesById[$category->getId()], $this->instancesByKey[$category->getKey()]);
        return $this;
    }
}
