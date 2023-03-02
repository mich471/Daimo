<?php

namespace Amasty\CustomerAttributes\Model\ResourceModel\Relation;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'Amasty\CustomerAttributes\Model\Relation',
            'Amasty\CustomerAttributes\Model\ResourceModel\Relation'
        );
    }
}
