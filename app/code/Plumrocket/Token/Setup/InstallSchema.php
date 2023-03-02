<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Token
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Token\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Plumrocket\Token\Model\ResourceModel\Customer as CustomerResourceToken;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $tokenTable = $installer->getConnection()
            ->newTable($installer->getTable(CustomerResourceToken::MAIN_TABLE_NAME))
            ->addColumn(
                CustomerResourceToken::MAIN_TABLE_ID_FIELD_NAME,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Token ID'
            )
            ->addColumn(
                'type_key',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Token type'
            )
            ->addColumn(
                'token_hash',
                Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'Token hash'
            )
            ->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Customer entity Id'
            )
            ->addColumn(
                'email',
                Table::TYPE_TEXT,
                255,
                [],
                'Main recipient email'
            )
            ->addColumn(
                'create_at',
                Table::TYPE_DATE,
                null,
                ['nullable' => false],
                'Date of token creation'
            )
            ->addColumn(
                'expire_at',
                Table::TYPE_DATE,
                null,
                ['nullable' => false],
                'Date for validation and delete'
            )
            ->addColumn(
                'additional_data',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Field for saving additional data'
            )
            ->setComment('Plumrocket Tokens');

        $installer->getConnection()->createTable($tokenTable);

        $installer->endSetup();
    }
}
