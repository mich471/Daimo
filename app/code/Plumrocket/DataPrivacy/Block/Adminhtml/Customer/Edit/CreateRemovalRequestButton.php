<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Block\Adminhtml\Customer\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * @since 3.2.0
 */
class CreateRemovalRequestButton implements ButtonProviderInterface
{

    /**
     * Get button options.
     *
     * @return array
     */
    public function getButtonData(): array
    {
        return [
            'label'          => __('Delete Data & Notify Customer'),
            'class'          => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order'     => 10,
            'aclResource'    => 'Plumrocket_GDPR::removalrequests',
        ];
    }
}
