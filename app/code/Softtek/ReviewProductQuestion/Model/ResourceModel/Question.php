<?php

namespace Softtek\ReviewProductQuestion\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Question extends AbstractDb
{

    protected function _construct()
    {
        $this->_init('phpcuong_product_question', 'question_id');
    }
}
