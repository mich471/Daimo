<?php

namespace Softtek\ReviewProductQuestion\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use Softtek\ReviewProductQuestion\Model\ResourceModel\Question as QuestionResourceModel;

class Question extends AbstractExtensibleModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(QuestionResourceModel::class);
    }

    /**
     * @return array
     */
    public function getCustomAttributesCodes()
    {
        return array('question_id', 'question_detail', 'question_status_id', 'product_name_global');
    }
}
