<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Helper;

use Plumrocket\Base\Model\Extensions\Information;

/**
 * @deprecated since 2.5.0 - this class will be removed
 */
class Data extends Main
{
    /**
     * @var string
     */
    protected $_configSectionId = Information::CONFIG_SECTION;

    /**
     * Receive true if Plumrocket module is enabled
     *
     * @param  string $store
     * @return bool
     * @suspendWarning
     * @noinspection PhpUnusedParameterInspection
     */
    public function moduleEnabled($store = null)
    {
        return true;
    }
}
