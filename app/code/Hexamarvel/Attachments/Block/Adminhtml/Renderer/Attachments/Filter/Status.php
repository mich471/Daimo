<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Attachments
 */
namespace Hexamarvel\Attachments\Block\Adminhtml\Renderer\Attachments\Filter;

class Status extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Select
{
    /**
     * @return array
     */
    protected function _getOptions()
    {
        $options = [
            ['value' => '', 'label' => ''],
            ['value' => '0', 'label' => 'Disabled'],
            ['value' => '1', 'label' => 'Enabled']
        ];

        return $options;
    }
}
