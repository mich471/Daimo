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

use Magento\Framework\Data\Form\Element\AbstractElement;

class AbstractBlock extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Template path
     */
    public $template = "system/config/button.phtml";

    /**
     * Button Label
     */
    public $buttonLabel;

    /**
     * @var \Plumrocket\GeoIPLookup\Helper\Config
     */
    public $configHelper;

    /**
     * @param \Plumrocket\GeoIPLookup\Helper\Config   $configHelper
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array                                   $data
     */
    public function __construct(
        \Plumrocket\GeoIPLookup\Helper\Config $configHelper,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configHelper = $configHelper;
    }

    /**
     * @return $this|\Magento\Config\Block\System\Config\Form\Field
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate($this->template);
        }
        return $this;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $buttonLabel = !empty($originalData['button_label']) ? $originalData['button_label'] : $this->buttonLabel;
        $this->addData(
            [
                'button_label' => __($buttonLabel),
                'html_id'      => $element->getHtmlId(),
                'onclick'      => $this->getOnclick($element->getHtmlId())
            ]
        );

        return $this->_toHtml();
    }

    /**
     * @return string
     */
    public function getOnclick($htmlId = null)
    {
        return 'return false;';
    }
}
