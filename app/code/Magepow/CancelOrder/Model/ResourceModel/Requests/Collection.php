<?php
namespace Magepow\CancelOrder\Model\ResourceModel\Requests;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'magepow_cancelorder_requests_collection';
    protected $_eventObject = 'requests_collection';

    /**
     * Define the resource model & the model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magepow\CancelOrder\Model\Requests', 'Magepow\CancelOrder\Model\ResourceModel\Requests');
    }
}
