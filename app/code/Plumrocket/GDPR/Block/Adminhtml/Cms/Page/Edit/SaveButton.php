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

namespace Plumrocket\GDPR\Block\Adminhtml\Cms\Page\Edit;

use Magento\Cms\Block\Adminhtml\Page\Edit\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var \Plumrocket\GDPR\Model\Magento\VersionProvider
     */
    private $versionProvider;

    /**
     * SaveAndContinueButton constructor.
     *
     * @param \Magento\Backend\Block\Widget\Context          $context
     * @param \Magento\Cms\Api\PageRepositoryInterface       $pageRepository
     * @param \Plumrocket\GDPR\Model\Magento\VersionProvider $versionProvider
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepository,
        \Plumrocket\GDPR\Model\Magento\VersionProvider $versionProvider
    ) {
        parent::__construct($context, $pageRepository);
        $this->versionProvider = $versionProvider;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        if ($this->versionProvider->isMagentoVersionBelow('2.3.0')) {
            return [
                'label' => __('Save Page'),
                'class' => 'save primary',
                'style' => 'display:none;',
                'on_click' => 'confirmSaveCmsPage("save")',
                'sort_order' => 90,
            ];
        }

        return [];
    }
}
