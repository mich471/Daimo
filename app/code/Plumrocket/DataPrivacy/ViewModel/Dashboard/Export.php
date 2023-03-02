<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\ViewModel\Dashboard;

/**
 * @since 3.1.0
 */
class Export extends UserAction
{

    /**
     * @return string
     */
    public function getExportUrl(): string
    {
        if ($this->isLoggedIn()) {
            return $this->urlBuilder->getUrl('pr_data_privacy/customer/export');
        }
        return $this->urlBuilder->getUrl('pr_data_privacy/guest/export', ['token' => $this->getSecureToken()]);
    }
}
