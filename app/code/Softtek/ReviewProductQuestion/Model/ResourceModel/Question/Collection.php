<?php

namespace Softtek\ReviewProductQuestion\Model\ResourceModel\Question;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Softtek\ReviewProductQuestion\Model\Question;
use Softtek\ReviewProductQuestion\Model\ResourceModel\Question as QuestionResourceModel;

class Collection extends AbstractCollection implements SearchResultInterface
{

    protected function _construct()
    {
        $this->_init(Question::class, QuestionResourceModel::class);
    }

    public function setItems(array $items = null)
    {
        return $this;
    }

    public function getAggregations()
    {
        return $this->aggregations;
    }

    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    public function getSearchCriteria()
    {
        return $this->searchCriteria;
    }

    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria)
    {
        $this->searchCriteria = $searchCriteria;
        return $this;
    }

    public function getTotalCount()
    {
        return $this->getSize();
    }

    public function setTotalCount($totalCount)
    {
        $this->setSize($totalCount);
        return $this;
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->join(
            ['name_attribute' => $this->getTable('eav_attribute')],
            'name_attribute.attribute_code = \'name\' AND name_attribute.entity_type_id = 4',
            ['name_attribute.attribute_id']
        );
        $this->getSelect()->join(
            ['product_names' => $this->getTable('catalog_product_entity_varchar')],
            'main_table.entity_pk_value = product_names.entity_id AND product_names.store_id = 0 AND product_names.attribute_id = name_attribute.attribute_id',
            ['product_name_global' => 'product_names.value']
        );
    }
}
