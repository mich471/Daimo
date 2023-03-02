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
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Model\ResourceModel\ConsentLog\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Plumrocket\CookieConsent\Api\Data\ConsentLogInterface;
use Plumrocket\CookieConsent\Model\ResourceModel\ConsentLog as ConsentLogResource;
use Psr\Log\LoggerInterface;

class Collection extends SearchResult
{
    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(// @codingStandardsIgnoreLine $mainTable and $resourceModel specified here
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        $mainTable = ConsentLogResource::MAIN_TABLE_NAME,
        $resourceModel = ConsentLogResource::class
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel
        );
    }

    /**
     * Initialization here
     *
     * @return void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addCustomData();
    }

    /**
     * Add custom data to collection
     *
     * @return $this
     */
    public function addCustomData()
    {
        $this->getSelect()
             ->joinLeft(
                 ['cg' => $this->getTable('customer_grid_flat')],
                 'cg.entity_id = main_table.customer_id',
                 ['cg.name as customer_name', 'cg.email as customer_email', 'cg.entity_id as customer_exist']
             )->joinLeft(
                 ['sw' => $this->getTable('store_website')],
                 'sw.website_id = main_table.website_id',
                 ['sw.name as website']
             );

        $this->getSelect()->columns(
            ['merge_email' => new \Zend_Db_Expr('IF(ISNULL(`cg`.`email`), `main_table`.`guest_email`, `cg`.`email`)')]
        );

        $filtersMap = [
            ConsentLogResource::ID_FIELD_NAME => 'main_table.' . ConsentLogResource::ID_FIELD_NAME,
            ConsentLogInterface::CUSTOMER_ID  => 'main_table.' . ConsentLogInterface::CUSTOMER_ID,
            'customer_name'                   => 'cg.name',
            'customer_exist'                  => 'cg.entity_id',
            'website'                         => 'sw.name',
            ConsentLogInterface::CREATED_AT   => 'main_table.' . ConsentLogInterface::CREATED_AT,
            ConsentLogInterface::IP_ADDRESS   => 'main_table.' . ConsentLogInterface::IP_ADDRESS,
            ConsentLogInterface::SETTINGS     => 'main_table.' . ConsentLogInterface::SETTINGS,
        ];

        foreach ($filtersMap as $filter => $alias) {
            $this->addFilterToMap($filter, $alias);
        }

        return $this;
    }

    /**
     * Custom filter for 'merge_email' field
     *
     * @param array|string $field
     * @param null         $condition
     * @return $this|\Plumrocket\CookieConsent\Model\ResourceModel\ConsentLog\Grid\Collection
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ('merge_email' === $field) {
            $this->getSelect()->where(
                "(`cg`.`email` LIKE ? OR (`main_table`.`guest_email` LIKE ? AND `cg`.`email` IS NULL))",
                $condition['like']
            );
            return $this;
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * @param \Magento\Framework\DataObject|\Magento\Framework\Model\AbstractModel $item
     * @return \Magento\Framework\DataObject
     */
    protected function beforeAddLoadedItem(DataObject $item)
    {
        $this->getResource()->unserializeDataProviderDocumentFields($item);
        return parent::beforeAddLoadedItem($item);
    }
}
