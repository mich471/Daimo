<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Attachments
 */
namespace Hexamarvel\Attachments\Model\ResourceModel\Attachments;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\Abstractcollection
{
    /**
     * @var primaryId
     */
    protected $_idFieldName = 'id';

    public function _construct()
    {
        $this->_init(
            \Hexamarvel\Attachments\Model\Attachments::class,
            \Hexamarvel\Attachments\Model\ResourceModel\Attachments::class
        );
    }
}
