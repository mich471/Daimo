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
class Delete extends UserAction
{

    /**
     * @return string
     */
    public function getDeleteUrl(): string
    {
        if ($this->isLoggedIn()) {
            return $this->urlBuilder->getUrl('pr_data_privacy/customer/delete');
        }
        return $this->urlBuilder->getUrl('pr_data_privacy/guest/delete', ['token' => $this->getSecureToken()]);
    }
}
