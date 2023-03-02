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
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Block\Adminhtml\Consent\Checkbox\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData() : array
    {
        $data = [];

        if ($this->getCheckboxId()) {
            $onClick = 'deleteConfirm(\''
                . __('Are you sure you want to do this?')
                . '\', \''
                . $this->getDeleteUrl()
                . '\')';

            $data = [
                'label' => __('Delete Checkbox'),
                'class' => 'delete',
                'on_click' => $onClick,
                'sort_order' => 20,
            ];
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl() : string
    {
        return $this->getUrl('prgdpr/consent_checkbox/delete', ['entity_id' => $this->getCheckboxId()]);
    }
}
