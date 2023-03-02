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

namespace Plumrocket\GeoIPLookup\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Plumrocket\GeoIPLookup\Model\Data\Import\Maxmindsplit;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var \Plumrocket\GeoIPLookup\Model\Data\Import\Maxmindsplit
     */
    private $maxmindSplit;

    /**
     * UpgradeSchema constructor.
     */
    public function __construct(
        Maxmindsplit $maxmindSplit
    ) {
        $this->maxmindSplit = $maxmindSplit;
    }

    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->maxmindSplit->splitMaxmindTable($setup);
        }

        $setup->endSetup();
    }
}
