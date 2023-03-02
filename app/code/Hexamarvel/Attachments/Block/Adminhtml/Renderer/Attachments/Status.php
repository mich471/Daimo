<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Attachments
 */
namespace Hexamarvel\Attachments\Block\Adminhtml\Renderer\Attachments;

use Magento\Framework\DataObject;

class Status extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @param  DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        return ($value) ? __('Enabled') : __('Disabled');
    }
}
