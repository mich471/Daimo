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
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Plumrocket\GDPR\Model\ResourceModel\Checkbox as CheckboxResource;
use Plumrocket\GDPR\Model\ResourceModel\Consent\Location as ConsentLocationResource;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        $connection = $setup->getConnection();

        /**
         * Version 1.2.0
         */
        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $connection->addColumn(
                $setup->getTable('plumrocket_gdpr_consents_log'),
                'action',
                [
                    'type' => Table::TYPE_SMALLINT,
                    'nullable' => true,
                    'comment' => 'Action',
                ]
            );

            $connection->update(
                $setup->getTable('plumrocket_gdpr_consents_log'),
                ['action' => 1],
                ['action IS NULL']
            );
        }

        /**
         * Version 1.4.0
         */
        if (version_compare($context->getVersion(), '1.4.0', '<')) {
            $connection->addColumn(
                $setup->getTable('plumrocket_gdpr_consents_log'),
                'checkbox_id',
                [
                    'type'     => Table::TYPE_INTEGER,
                    'nullable' => false,
                    'comment'  => 'Checkbox ID',
                    'after'    => 'location',
                    'unsigned' => true,
                    'default' => 0,
                ]
            );

            /**
             * Create table for consent locations
             */
            $consentLocationTable = $connection->newTable(
                $setup->getTable(ConsentLocationResource::MAIN_TABLE_NAME)
            )->addColumn(
                ConsentLocationResource::MAIN_TABLE_ID_FIELD_NAME,
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Record Identifier'
            )->addColumn(
                'location_key',
                Table::TYPE_TEXT,
                32,
                [
                    'nullable' => false,
                    'primary' => true
                ],
                'Location Key'
            )->addColumn(
                'type',
                Table::TYPE_INTEGER,
                1,
                [
                    'nullable' => false,
                    'default' => 2,
                ],
                'Status'
            )->addColumn(
                'visible',
                Table::TYPE_BOOLEAN,
                null,
                [
                    'nullable' => false,
                    'default' => 0,
                ],
                'Visibility Status'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                64,
                ['nullable' => false],
                'Location Name'
            )->addColumn(
                'description',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Internal Note'
            )->addIndex(
                $setup->getIdxName(
                    ConsentLocationResource::MAIN_TABLE_NAME,
                    ['location_key'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['location_key'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            );

            $connection->createTable($consentLocationTable);

            $checkboxEntity = \Plumrocket\GDPR\Model\Checkbox::ENTITY;

            /**
             * Create table for checkboxes
             */
            $checkboxTable = $connection->newTable(
                $setup->getTable($setup->getTable($checkboxEntity . '_entity'))
            )->addColumn(
                CheckboxResource::ID_FIELD_NAME,
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Record Identifier'
            )->addColumn(
                'location_key',
                Table::TYPE_TEXT,
                32,
                [
                    'nullable' => false,
                ],
                'Location Key'
            )->addColumn(
                'internal_note',
                Table::TYPE_TEXT,
                '2M',
                ['nullable' => false],
                'Internal Note'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Creation Time'
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Update Time'
            );

            $connection->createTable($checkboxTable);

            $tableEntityText = $connection->newTable(
                $setup->getTable($checkboxEntity . '_entity_text')
            )
            ->addColumn(
                'value_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Value ID'
            )
            ->addColumn(
                'attribute_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Attribute Id'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Store ID'
            )
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Entity Id'
            )
            ->addColumn(
                'value',
                Table::TYPE_TEXT,
                255,
                [],
                'value'
            )
            ->addIndex(
                $setup->getIdxName(
                    $checkboxEntity . '_entity_text',
                    ['entity_id', 'attribute_id', 'store_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['entity_id', 'attribute_id', 'store_id'],
                ['type'=>\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addIndex(
                $setup->getIdxName(
                    $checkboxEntity . '_entity_text',
                    ['store_id']
                ),
                ['store_id']
            )
            ->addIndex(
                $setup->getIdxName(
                    $checkboxEntity . '_entity_text',
                    ['attribute_id']
                ),
                ['attribute_id']
            )
            ->addForeignKey(
                $setup->getFkName(
                    $checkboxEntity . '_entity_text',
                    'attribute_id',
                    'eav_attribute',
                    'attribute_id'
                ),
                'attribute_id',
                $setup->getTable('eav_attribute'),
                'attribute_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName(
                    $checkboxEntity . '_entity_text',
                    'entity_id',
                    $checkboxEntity . '_entity',
                    'entity_id'
                ),
                'entity_id',
                $setup->getTable($checkboxEntity . '_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName(
                    $checkboxEntity . '_entity_text',
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->setComment('PR Checkbox text Attribute Backend Table');
            $setup->getConnection()->createTable($tableEntityText);

            $tableEntityInt = $connection->newTable(
                $setup->getTable($checkboxEntity . '_entity_int')
            )
            ->addColumn(
                'value_id',
                Table::TYPE_INTEGER,
                null,
                ['identity'=>true, 'nullable'=>false, 'primary'=>true],
                'Value ID'
            )
            ->addColumn(
                'attribute_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned'=>true, 'nullable'=>false, 'default'=>'0'],
                'Attribute Id'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned'=>true, 'nullable'=>false, 'default'=>'0'],
                'Store ID'
            )
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable'=>false, 'default'=>'0'],
                'Entity Id'
            )
            ->addColumn(
                'value',
                Table::TYPE_INTEGER,
                null,
                [],
                'value'
            )
            ->addIndex(
                $setup->getIdxName(
                    $checkboxEntity . '_entity_int',
                    ['entity_id', 'attribute_id', 'store_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['entity_id', 'attribute_id', 'store_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addIndex(
                $setup->getIdxName(
                    $checkboxEntity . '_entity_int',
                    ['store_id']
                ),
                ['store_id']
            )
            ->addIndex(
                $setup->getIdxName(
                    $checkboxEntity . '_entity_int',
                    ['attribute_id']
                ),
                ['attribute_id']
            )
            ->addForeignKey(
                $setup->getFkName(
                    $checkboxEntity . '_entity_int',
                    'attribute_id',
                    'eav_attribute',
                    'attribute_id'
                ),
                'attribute_id',
                $setup->getTable('eav_attribute'),
                'attribute_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName(
                    $checkboxEntity . '_entity_int',
                    'entity_id',
                    $checkboxEntity . '_entity',
                    'entity_id'
                ),
                'entity_id',
                $setup->getTable($checkboxEntity . '_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName(
                    $checkboxEntity . '_entity_int',
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->setComment('PR Checkbox Int Attribute Backend Table');
            $setup->getConnection()->createTable($tableEntityInt);
        }

        if (version_compare($context->getVersion(), '1.4.6', '<')) {
            $connection->changeColumn(
                $setup->getTable(ConsentLocationResource::MAIN_TABLE_NAME),
                'visible',
                'visible',
                ['default' => '1', 'type' => Table::TYPE_BOOLEAN]
            );
        }

        if (version_compare($context->getVersion(), '1.4.7', '<')) {
            $connection->changeColumn(
                $setup->getTable(CheckboxResource::MAIN_TABLE_NAME),
                'location_key',
                'location_key',
                ['size' => 255, 'type' => Table::TYPE_TEXT]
            );
        }

        /**
         * Version 1.6.0
         */
        if (version_compare($context->getVersion(), '1.6.0', '<')) {
            /**
             * Add New Column
             */
            $connection->addColumn(
                $setup->getTable('plumrocket_gdpr_export_log'),
                'customer_email',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 254,
                    'nullable' => false,
                    'comment' => 'Customer Email'
                ]
            );

            /**
             * Drop Foreign Key
             */
            $connection->dropForeignKey(
                $setup->getTable('plumrocket_gdpr_export_log'),
                $setup->getFkName(
                    'plumrocket_gdpr_export_log',
                    'customer_id',
                    'customer_entity',
                    'entity_id'
                )
            );
        }

        /**
         * Version 1.7.2
         */
        if (version_compare($context->getVersion(), '1.7.2', '<')) {
            $connection->dropForeignKey(
                $setup->getTable('plumrocket_gdpr_consents_log'),
                $setup->getFkName(
                    'plumrocket_gdpr_consents_log',
                    'customer_id',
                    'customer_entity',
                    'entity_id'
                )
            );

            $connection->addColumn(
                $setup->getTable('plumrocket_gdpr_consents_log'),
                'email',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 254,
                    'nullable' => true,
                    'comment' => 'Customer and Guest Email'
                ]
            );

            $connection->changeColumn(
                $setup->getTable('plumrocket_gdpr_consents_log'),
                'customer_id',
                'customer_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'size' => 11,
                    'unsigned' => false,
                    'nullable' => true
                ]
            );
        }

        /**
         * Version 3.0.3
         */
        if (version_compare($context->getVersion(), '3.0.3', '<')) {
            $connection->changeColumn(
                $setup->getTable(CheckboxResource::MAIN_TABLE_NAME . '_text'),
                'value',
                'value',
                [
                    'type' => Table::TYPE_TEXT,
                    'size' => '10000'
                ]
            );
            $connection->changeColumn(
                $setup->getTable('plumrocket_gdpr_consents_log'),
                'label',
                'label',
                [
                    'type' => Table::TYPE_TEXT,
                    'size' => '10000',
                ]
            );
        }

        $setup->endSetup();
    }
}
