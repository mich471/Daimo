<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Plumrocket\DataPrivacy\Model\Account\Data\Anonymizer;
use Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest;
use Psr\Log\LoggerInterface;

/**
 * @since 3.2.0
 */
class Collection extends SearchResult
{

    /**
     * @var \Plumrocket\DataPrivacy\Model\Account\Data\Anonymizer
     */
    private $anonymizer;

    /**
     * @param \Plumrocket\DataPrivacy\Model\Account\Data\Anonymizer        $anonymizer
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory
     * @param \Psr\Log\LoggerInterface                                     $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param string                                                       $mainTable
     * @param string                                                       $resourceModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(// @codingStandardsIgnoreLine $mainTable and $resourceModel specified here
        Anonymizer $anonymizer,
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        $mainTable = RemovalRequest::MAIN_TABLE_NAME,
        $resourceModel = RemovalRequest::class
    ) {
        $this->anonymizer = $anonymizer;

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
    protected function _initSelect()// @codingStandardsIgnoreLine we need to extend parent method
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
        $anonymizationKey = $this->anonymizer->getKey();

        $customerNameColumn = 'IFNULL(cg.name, '
            . 'CONCAT("'.$anonymizationKey.'", "-", main_table.customer_id))'
            . ' as customer_name';

        $customerExistColumn = 'cg.entity_id as customer_exist';

        $canceledByColumn = 'IFNULL('
            . 'CONCAT(au.firstname, " ", au.lastname, " [Id:", au.user_id, "]"),'
            . ' main_table.cancelled_by)'
            . ' as cancelled_by';

        $createdByColumn = 'IFNULL('
            . 'CONCAT(au2.firstname, " ", au2.lastname, " [Id:", au2.user_id, "]"),'
            . ' \'Customer\')'
            . ' as created_by';

        $this->getSelect()
            ->joinLeft(
                ['cg' => $this->getTable('customer_grid_flat')],
                'cg.entity_id = main_table.customer_id',
                [$customerNameColumn, $customerExistColumn]
            )->joinLeft(
                ['sw' => $this->getTable('store_website')],
                'sw.website_id = main_table.website_id',
                ['sw.name as website']
            )->joinLeft(
                ['au' => $this->getTable('admin_user')],
                'au.user_id = main_table.cancelled_by',
                [$canceledByColumn]
            )->joinLeft(
                ['au2' => $this->getTable('admin_user')],
                'au2.user_id = main_table.admin_id',
                [$createdByColumn]
            );

        $filtersMap = [
            'request_id' => 'main_table.request_id',
            'customer_id' => 'main_table.customer_id',
            'customer_email' => 'main_table.customer_email',
            'customer_name' => 'cg.name',
            'customer_exist' => 'cg.entity_id',
            'website' => 'sw.name',
            'created_at' => 'main_table.created_at',
            'customer_ip' => 'main_table.customer_ip',
            'cancelled_at' => 'main_table.cancelled_at',
            'cancelled_by' => 'main_table.cancelled_by',
            'scheduled_at' => 'main_table.scheduled_at',
            'status' => 'main_table.status',
        ];

        foreach ($filtersMap as $filter => $alias) {
            $this->addFilterToMap($filter, $alias);
        }

        return $this;
    }
}
