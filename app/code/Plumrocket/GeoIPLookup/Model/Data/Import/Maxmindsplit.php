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

namespace Plumrocket\GeoIPLookup\Model\Data\Import;

use Plumrocket\GeoIPLookup\Model\ResourceModel\Maxmind;

class Maxmindsplit
{
    /**
     * Connection
     */
    private $originalTable;

    /**
     * @return array
     */
    public function getSplitData()
    {
        return [
            ['16777216','1052362784'],
            ['1052362816','1347838411'],
            ['1347838412','1600739328'],
            ['1600739840','2535995904'],
            ['2535996416','3195109120'],
            ['3195109376','3758096128']
        ];
    }

    /**
     * @return void
     */
    public function splitMaxmindTable($connection, $isInstaller = true)
    {
        $methodName = $isInstaller ? 'getTable' : 'getTableName';

        $this->originalTable = $connection->{$methodName}(Maxmind::MAIN_TABLE_NAME);
        $connection = $connection->getConnection();

        foreach ($this->getSplitData() as $key => $value) {
            $limitFrom = $value[0];
            $limitTo = $value[1];
            $partialTableName = $this->createTableLike($connection, $key);

            $select = $connection->select()
                ->from(
                    ['main' => $this->originalTable]
                )->where("main.ip_from >= $limitFrom AND main.ip_from <= $limitTo");

            $insertSelect = $connection->insertFromSelect($select, $partialTableName);
            $connection->query($insertSelect);
        }

        $connection->truncateTable($this->originalTable);
    }

    /**
     * @param $connection
     * @param $partNumber
     * @return string
     */
    private function createTableLike($connection, $partNumber)
    {
        $partialTableName = $this->originalTable . "_part" . $partNumber;

        if (!$connection->isTableExists($partialTableName)) {
            $connection->query("CREATE TABLE " . $partialTableName .  " LIKE " . $this->originalTable);
        } else {
            $connection->truncateTable($partialTableName);
        }

        return $partialTableName;
    }
}
