<?php
namespace Softtek\CatalogSearch\Plugin\Model\Search;

use Magento\CatalogSearch\Model\Search\RequestGenerator;

class RequestGeneratorPlugin
{
    /**
     * @inheritdoc
     */
    public function afterGenerate(RequestGenerator $requestGenerator, $result)
    {
        if (!isset($result['advanced_search_container']['queries']['advanced_search_container']['queryReference'])) return $result;

        foreach ($result['advanced_search_container']['queries']['advanced_search_container']['queryReference'] as $k => $v) {
            if ($v['ref'] == 'name_query') {
                $result['advanced_search_container']['queries']['advanced_search_container']['queryReference'][$k]['clause'] = 'should';
            }
        }

        return $result;
    }
}
