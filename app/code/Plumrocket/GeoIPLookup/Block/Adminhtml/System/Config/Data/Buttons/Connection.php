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
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\GeoIPLookup\Block\Adminhtml\System\Config\Data\Buttons;

use Plumrocket\GeoIPLookup\Helper\Config;

class Connection extends AbstractBlock
{
    /**
     * Button Label
     */
    public $buttonLabel = 'Test Connection';

    /**
     * @param $htmlId
     * @return null|string
     */
    public function getOnclick($htmlId = null)
    {
        return sprintf(
            'window.prGeoIptestApiConnection(\'%s\', \'%s\'); return false;',
            $this->configHelper->getIpApiUrl(Config::DEFAULT_IP, false),
            $htmlId
        );
    }
}
