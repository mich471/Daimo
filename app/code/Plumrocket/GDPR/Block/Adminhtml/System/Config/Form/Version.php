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
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Block\Adminhtml\System\Config\Form;

/**
 * @deprecated since 3.1.0
 */
class Version extends \Plumrocket\Base\Block\Adminhtml\System\Config\Form\Version
{
    /**
     * Wiki link
     *
     * @var string
     */
    protected $wikiLink = '';

    /**
     * Module Name
     *
     * @var string
     */
    protected $moduleTitle = 'Data Privacy';

    /**
     * Receive extension information html
     *
     * @todo remove after moving to Data Privacy module
     * @return string
     */
    public function getModuleInfoHtml()
    {
        $moduleName = $this->getModuleName();
        $this->setData('module_name', 'Plumrocket_DataPrivacy');

        $html = parent::getModuleInfoHtml();

        $this->setData('module_name', $moduleName);

        return $html;
    }
}
