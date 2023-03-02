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

namespace Plumrocket\CookieConsent\Model\ResourceModel;

use Magento\Eav\Model\Config;
use Magento\Eav\Model\ResourceModel\Entity\Attribute as MagentoEavResourceAttribute;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Plumrocket\CookieConsent\Model\Eav\Attribute as CookieConsentEavAttribute;
use Psr\Log\LoggerInterface;

/**
 * @since 1.0.0
 */
abstract class AbstractAttributeCollection extends MagentoEavResourceAttribute\Collection
{
    const EAV_ATTRIBUTE_ADDITIONAL_TABLE = 'plumrocket_eav_attribute_additional';

    /**
     * @var string
     */
    protected $entityCode;

    /**
     * Entity factory
     *
     * @var \Magento\Eav\Model\EntityFactory
     */
    public $eavEntityFactory;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        EntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        Config $eavConfig,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->eavEntityFactory = $eavEntityFactory;

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $eavConfig, $connection, $resource);
    }

    /**
     * Resource model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            CookieConsentEavAttribute::class,
            MagentoEavResourceAttribute::class
        );
    }

    /**
     * @inheritDoc
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(
            ['main_table' => $this->getResource()->getMainTable()]
        )->where(
            'main_table.entity_type_id=?',
            $this->eavEntityFactory->create()->setType($this->entityCode)->getTypeId()
        )->join(
            ['additional_table' => $this->getTable(self::EAV_ATTRIBUTE_ADDITIONAL_TABLE)],
            'additional_table.attribute_id = main_table.attribute_id'
        );

        return $this;
    }

    /**
     * Specify attribute entity type filter
     *
     * @param int $typeId
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setEntityTypeFilter($typeId)
    {
        return $this;
    }
}
