<?php
namespace Amasty\CustomerAttributes\Block\Data\Form\Element;

class Boolean extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Boolean
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $values = $this->getValues();
        if (!$this->getRequired()) {
            array_unshift($values, ['label' => ' ', 'value' => '']);
        }
        $this->setValues($values);
    }
}
