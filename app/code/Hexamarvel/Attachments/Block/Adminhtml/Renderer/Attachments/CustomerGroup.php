<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Attachments
 */
namespace Hexamarvel\Attachments\Block\Adminhtml\Renderer\Attachments;

use Magento\Framework\DataObject;

class CustomerGroup extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Customer\Model\GroupFactory
     */
    protected $customerGroup;

    /**
     * @param \Magento\Customer\Model\GroupFactory $customerGroup
     */
    public function __construct(
        \Magento\Customer\Model\GroupFactory $customerGroup
    ) {
        $this->_customerGroup = $customerGroup;
    }

    /**
     * @param  DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        return $this->prepareItem($value);
    }

    /**
     * @param string $value
     * @return string $result
     */
    protected function prepareItem($value)
    {
        $result = '';
        foreach (explode(',', $value) as $key => $groupId) {
            $result .= $this->_customerGroup->create()->load($groupId)->getCustomerGroupCode().'<br />';
        }

        return $result;
    }
}
