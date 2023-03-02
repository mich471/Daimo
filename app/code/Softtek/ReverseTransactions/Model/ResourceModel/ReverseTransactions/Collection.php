<?php
/**
 * Softtek ReverseTransactions Module
 *
 * @package SofttekOneclick
 * @author Juan C Flores <juan.floress@softtek.com>
 * @copyright Softtek 2020
 */
namespace Softtek\ReverseTransactions\Model\ResourceModel\ReverseTransactions;

use Softtek\ReverseTransactions\Model\ReverseTransactions as Model;
use Softtek\ReverseTransactions\Model\ResourceModel\ReverseTransactions as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Init
     */
    protected function _construct() // phpcs:ignore PSR2.Methods.MethodDeclaration
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
