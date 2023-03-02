<?php
/**
 * Softtek ReverseTransactions Module
 *
 * @package SofttekReverseTransactions
 * @author Juan C Flores <juan.floress@softtek.com>
 * @copyright Softtek 2020
 */
namespace Softtek\ReverseTransactions\Model\ResourceModel;

class ReverseTransactions extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Init
     */
    protected function _construct() // phpcs:ignore PSR2.Methods.MethodDeclaration
    {
        $this->_init('softtek_reverse_transactions', 'id');
    }
}
