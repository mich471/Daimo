<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Api;

use Plumrocket\Base\Api\Data\ExtensionInformationInterface;

/**
 * Allow easily retrieve information about installed modules
 *
 * @since 2.3.0
 * @deprecated since 2.5.0
 * @see \Plumrocket\Base\Api\Data\ExtensionInformationInterface
 */
interface ModuleInformationInterface extends ExtensionInformationInterface
{
    /**
     * Retrieve name of module, e.g. Twitter & Facebook Login
     *
     * @deprecated since 2.4.0
     * @see getTitle()
     * @return string
     */
    public function getOfficialName(): string;
}
