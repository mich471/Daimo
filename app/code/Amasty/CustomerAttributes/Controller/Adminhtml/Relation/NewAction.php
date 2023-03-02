<?php

namespace Amasty\CustomerAttributes\Controller\Adminhtml\Relation;

class NewAction extends \Amasty\CustomerAttributes\Controller\Adminhtml\Relation
{
    /**
     * @return void
     */
    public function execute()
    {
        return $this->_forward('edit');
    }
}
