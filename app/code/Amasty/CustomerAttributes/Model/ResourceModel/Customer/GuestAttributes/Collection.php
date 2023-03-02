<?php

namespace Amasty\CustomerAttributes\Model\ResourceModel\Customer\GuestAttributes;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Amasty\CustomerAttributes\Model\Customer\GuestAttributes',
            'Amasty\CustomerAttributes\Model\ResourceModel\Customer\GuestAttributes'
        );
    }
}
