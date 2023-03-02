<?php
namespace Softtek\CatalogSearch\Plugin\Block\Advanced;

use Magento\CatalogSearch\Block\Advanced\Result;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;

class ResultPlugin
{

    /**
     * @param LayerResolver $layerResolver
     */
    public function __construct(
        LayerResolver $layerResolver
    ) {
        $this->_catalogLayer = $layerResolver->get();
    }

    /**
     * @inheritdoc
     */
    public function afterSetListOrders(Result $advancedResult, $result)
    {
        /* @var $category \Magento\Catalog\Model\Category */
        $category = $this->_catalogLayer->getCurrentCategory();

        $availableOrders = $category->getAvailableSortByOptions();
        unset($availableOrders['position']);
        $availableOrders['relevance'] = __('Relevance');

        $advancedResult->getChildBlock('search_result_list')
            ->setAvailableOrders($availableOrders)
            ->setDefaultDirection('desc')
            ->setSortBy('relevance');
    }
}
