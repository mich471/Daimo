<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Attachments
 */
namespace Hexamarvel\Attachments\Model\Config\Source;

class DisplayArea implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array options
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'producttab', 'label' => __('Product Tab (if tabs are available)')],
            ['value' => 'productshortdesc', 'label' => __('Under Product Short Desc')]
        ];
    }
}
