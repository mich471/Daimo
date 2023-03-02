<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Attachments
 */
namespace Hexamarvel\Attachments\Model;

class Attachments extends \Magento\Framework\Model\AbstractModel
{
    public function _construct()
    {
        $this->_init(\Hexamarvel\Attachments\Model\ResourceModel\Attachments::class);
    }
}
