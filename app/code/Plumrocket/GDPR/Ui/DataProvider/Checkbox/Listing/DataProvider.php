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

namespace Plumrocket\GDPR\Ui\DataProvider\Checkbox\Listing;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Magento\Ui\DataProvider\AddFieldToCollectionInterface[]
     */
    private $addFieldStrategies;

    /**
     * @var \Magento\Ui\DataProvider\AddFilterToCollectionInterface[]
     */
    private $addFilterStrategies;

    /**
     * @var \Magento\Cms\Api\PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param string                                                          $name
     * @param string                                                          $primaryFieldName
     * @param string                                                          $requestFieldName
     * @param \Plumrocket\GDPR\Model\ResourceModel\Checkbox\CollectionFactory $collectionFactory
     * @param \Magento\Cms\Api\PageRepositoryInterface                        $pageRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder                    $searchCriteriaBuilder
     * @param \Magento\Ui\DataProvider\AddFieldToCollectionInterface[]        $addFieldStrategies
     * @param \Magento\Ui\DataProvider\AddFilterToCollectionInterface[]       $addFilterStrategies
     * @param array                                                           $meta
     * @param array                                                           $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Plumrocket\GDPR\Model\ResourceModel\Checkbox\CollectionFactory $collectionFactory,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
        $this->pageRepository = $pageRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (! $this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }

        $cmsPageTitles = $this->getCmsPageTitles($this->getCollection()->getColumnValues('cms_page_id'));

        $items = $this->getCollection()->toArray();
        $items = array_map(static function ($item) use ($cmsPageTitles) {
            $item['id'] = $item['entity_id'];
            $item['id_field_name'] = 'id';

            if (! empty($item['cms_page_id'])) {
                $item['cms_page'] = $cmsPageTitles[$item['cms_page_id']] ?? '';
            }

            return $item;
        }, $items);

        $data = [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items),
        ];

        return $data;
    }

    /**
     * Add field to select
     *
     * @param string|array $field
     * @param string|null $alias
     * @return void
     */
    public function addField($field, $alias = null)
    {
        if (isset($this->addFieldStrategies[$field])) {
            $this->addFieldStrategies[$field]->addField($this->getCollection(), $field, $alias);
        } else {
            parent::addField($field, $alias);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if (isset($this->addFilterStrategies[$filter->getField()])) {
            $this->addFilterStrategies[$filter->getField()]
                ->addFilter(
                    $this->getCollection(),
                    $filter->getField(),
                    [$filter->getConditionType() => $filter->getValue()]
                );
        } else {
            parent::addFilter($filter);
        }
    }

    /**
     * Load titles of CMS Pages
     *
     * @param array $cmsPageIds
     * @return array
     */
    private function getCmsPageTitles(array $cmsPageIds) : array
    {
        $pageTitles = [];

        if ($ids = array_unique(array_filter($cmsPageIds))) {
            $this->searchCriteriaBuilder->addFilter('page_id', $ids, 'in');
            $searchCriteria = $this->searchCriteriaBuilder->create();

            $result = $this->pageRepository->getList($searchCriteria);
            foreach ($result->getItems() as $cmsPage) {
                $pageTitles[$cmsPage->getId()] = $cmsPage->getTitle();
            }
        }

        return $pageTitles;
    }
}
