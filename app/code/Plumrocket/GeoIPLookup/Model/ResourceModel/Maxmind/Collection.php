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
 * @package     Plumrocket_GeoIPLookup
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GeoIPLookup\Model\ResourceModel\Maxmind;

use Plumrocket\GeoIPLookup\Model\Data\Import\Maxmindsplit;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var
     */
    private $maxmindsplit;

    /**
     * Collection constructor.
     *
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory
     * @param \Psr\Log\LoggerInterface                                     $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param Maxmindsplit                                                 $maxmindsplit
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Plumrocket\GeoIPLookup\Model\Data\Import\Maxmindsplit $maxmindsplit
    ) {
        $this->maxmindsplit = $maxmindsplit;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager);
    }

    /**
     * Collection Init
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init(
            \Plumrocket\GeoIPLookup\Model\Maxmind::class,
            \Plumrocket\GeoIPLookup\Model\ResourceModel\Maxmind::class
        );
    }

    /**
     * @param $ip
     * @return $this
     */
    public function addFilterByIp($ip)
    {
        $partitionalPrefix = '';

        foreach ($this->maxmindsplit->getSplitData() as $key => $value) {
            if ($ip >= $value[0] && $ip <= $value[1]) {
                $partitionalPrefix = '_part' . $key;
                break;
            }
        }

        $partitionalTable = $this->getMainTable() . $partitionalPrefix;

        $this->setMainTable($partitionalTable);
        $this->addFieldToFilter('ip_from', ['lteq' => $ip]);
        $this->addFieldToFilter('ip_to', ['gteq' => $ip]);

        return $this;
    }
}
