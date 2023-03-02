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
use Plumrocket\CookieConsent\Api\CookieRepositoryInterface;
use Plumrocket\CookieConsent\Api\Data\CookieInterface;
use Plumrocket\CookieConsent\Api\Data\CookieInterfaceFactory;
use Plumrocket\CookieConsent\Api\Data\CookieSearchResultsInterfaceFactory;
use Plumrocket\CookieConsent\Api\GetCookieIdByNameInterface;
use Plumrocket\CookieConsent\Model\ResourceModel\Cookie\CollectionFactory;

/**
 * @since 1.0.0
 */
class CookieRepository implements CookieRepositoryInterface
{
    private $instancesById = [];
    private $instancesByName = [];

    /**
     * @var \Plumrocket\CookieConsent\Api\Data\CookieInterfaceFactory
     */
    private $cookieFactory;

    /**
     * @var \Plumrocket\CookieConsent\Api\Data\CookieSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var \Plumrocket\CookieConsent\Model\ResourceModel\Cookie
     */
    private $resourceModel;

    /**
     * @var \Plumrocket\CookieConsent\Api\GetCookieIdByNameInterface
     */
    private $getCookieIdByName;

    /**
     * @var \Plumrocket\CookieConsent\Model\ResourceModel\Cookie\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * CookieRepository constructor.
     *
     * @param \Plumrocket\CookieConsent\Api\Data\CookieInterfaceFactory              $cookieFactory
     * @param \Plumrocket\CookieConsent\Api\Data\CookieSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Plumrocket\CookieConsent\Model\ResourceModel\Cookie                   $resourceModel
     * @param \Plumrocket\CookieConsent\Api\GetCookieIdByNameInterface               $getCookieIdByName
     * @param \Plumrocket\CookieConsent\Model\ResourceModel\Cookie\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface     $collectionProcessor
     */
    public function __construct(
        CookieInterfaceFactory $cookieFactory,
        CookieSearchResultsInterfaceFactory $searchResultsFactory,
        \Plumrocket\CookieConsent\Model\ResourceModel\Cookie $resourceModel,
        GetCookieIdByNameInterface $getCookieIdByName,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->cookieFactory = $cookieFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->resourceModel = $resourceModel;
        $this->getCookieIdByName = $getCookieIdByName;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(CookieInterface $cookie): CookieInterface
    {
        try {
            $this->clearLocalCache($cookie);
            $this->resourceModel->save($cookie);
        } catch (AttributeException $exception) {
            throw InputException::invalidFieldValue(
                $exception->getAttributeCode(),
                $cookie->getData($exception->getAttributeCode()),
                $exception
            );
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __('The cookie was unable to be saved. Please try again.'),
                $e
            );
        }

        $this->clearLocalCache($cookie);

        return $this->getById($cookie->getId(), $cookie->getStoreId());
    }

    /**
     * @inheritDoc
     */
    public function get(string $name, int $storeId = null, bool $forceReload = false): CookieInterface
    {
        if (! isset($this->instancesByName[$name][$storeId]) || $forceReload) {
            $cookieId = $this->getCookieIdByName->execute($name, $forceReload);
            if (! $cookieId) {
                throw new NoSuchEntityException(
                    __("The cookie that was requested doesn't exist. Verify the cookie and try again.")
                );
            }

            return $this->getById($cookieId);
        }

        return $this->instancesByName[$name][$storeId];
    }

    /**
     * @inheritDoc
     */
    public function getById(int $cookieId, int $storeId = null, bool $forceReload = false): CookieInterface
    {
        if (! isset($this->instancesById[$cookieId][$storeId]) || $forceReload) {
            /** @var \Plumrocket\CookieConsent\Model\Cookie $cookie */
            $cookie = $this->cookieFactory->create();
            if ($storeId !== null) {
                $cookie->setStoreId($storeId);
            }
            $cookie->load($cookieId);
            if (! $cookie->getId()) {
                throw new NoSuchEntityException(
                    __("The cookie that was requested doesn't exist. Verify the cookie and try again.")
                );
            }

            $this->saveToLocalCache($cookie, $storeId);
        }

        return $this->instancesById[$cookieId][$storeId];
    }

    /**
     * @inheritDoc
     */
    public function delete(CookieInterface $cookie): bool
    {
        $cookieId = $cookie->getId();
        $cookieKey = $cookie->getName();
        try {
            $this->clearLocalCache($cookie);
            $this->resourceModel->delete($cookie);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('The "%1" cookie couldn\'t be removed.', $cookieId)
            );
        }

        unset($this->instancesById[$cookieId], $this->instancesByName[$cookieKey]);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $id): bool
    {
        $cookie = $this->getById($id);
        return $this->delete($cookie);
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        /** @var \Plumrocket\CookieConsent\Model\ResourceModel\Cookie\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect('*');
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var \Plumrocket\CookieConsent\Api\Data\CookieSearchResultsInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    /**
     * @param \Plumrocket\CookieConsent\Api\Data\CookieInterface $cookie
     * @param int|null                                           $storeId
     * @return $this
     */
    private function saveToLocalCache(CookieInterface $cookie, int $storeId = null): CookieRepositoryInterface
    {
        $this->instancesById[$cookie->getId()][$storeId] = $cookie;
        $this->instancesByName[$cookie->getName()][$storeId] = $cookie;
        return $this;
    }

    /**
     * @param \Plumrocket\CookieConsent\Api\Data\CookieInterface $cookie
     * @return $this
     */
    private function clearLocalCache(CookieInterface $cookie): CookieRepositoryInterface
    {
        unset($this->instancesById[$cookie->getId()], $this->instancesByName[$cookie->getKey()]);
        return $this;
    }
}
