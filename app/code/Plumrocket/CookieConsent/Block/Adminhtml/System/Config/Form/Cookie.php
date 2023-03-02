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
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\CookieConsent\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Cookie extends Field
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * Cookie constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\App\RequestInterface $request
     * @param array                                   $data
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        array $data = []
    ) {
        $this->request = $request;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve element HTML markup
     * Argument $element must be specified
     * We need to extend parent method
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $params = [];
        $websiteId = (int) $this->request->getParam('website', 0);
        $storeId = (int) $this->request->getParam('store', 0);

        if ($websiteId) {
            $params['website'] = $websiteId;
        }

        if ($storeId) {
            $params['store'] = $storeId;
        }

        $url = $this->_urlBuilder->getUrl('adminhtml/system_config/edit/section/web', $params);

        return __(
            'To Enable, <a href="%1" target="_blank">Click here</a>, then open "Default Cookie Settings" section and' .
            ' set "Yes" next to the "Cookie Restriction Mode". Then save changes.',
            $url
        );
    }

    /**
     * Render inheritance checkbox (Use Default or Use Website)
     * Argument $element must be specified
     * We need to extend parent method
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _renderInheritCheckbox(AbstractElement $element)
    {
        return '';
    }
}
