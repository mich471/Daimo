<?php

namespace Amasty\CustomerAttributes\Model;

use \Magento\Framework\Model\AbstractModel;

class CustomerFormManager extends AbstractModel
{
    const REQUIRED_ON_FRONT = 2;
    const ORDER_OFFSET = 1000;

    public function __construct()
    {
        parent::_construct();
        $this->_init('Amasty\CustomerAttributes\Model\ResourceModel\CustomerFormManager');
    }
}
