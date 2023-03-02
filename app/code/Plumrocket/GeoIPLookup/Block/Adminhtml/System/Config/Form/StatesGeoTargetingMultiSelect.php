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

namespace Plumrocket\GeoIPLookup\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Element\Html\Select as HtmlSelect;
use Plumrocket\GeoIPLookup\Api\LocationsListInterface;
use Plumrocket\GeoIPLookup\Helper\Config;
use Plumrocket\GeoIPLookup\Model\System\Config\Source\GeoTargetingStates;

/**
 * @since 1.2.2
 */
class StatesGeoTargetingMultiSelect extends Field
{
    /**
     * @var \Plumrocket\GeoIPLookup\Model\System\Config\Source\GeoTargetingStates
     */
    private $geoTargetingStates;

    /**
     * @var \Plumrocket\GeoIPLookup\Helper\Config
     */
    private $config;

    /**
     * @param \Magento\Backend\Block\Template\Context                               $context
     * @param \Plumrocket\GeoIPLookup\Model\System\Config\Source\GeoTargetingStates $geoTargetingStates
     * @param \Plumrocket\GeoIPLookup\Helper\Config                                 $config
     * @param array                                                                 $data
     */
    public function __construct(
        Context $context,
        GeoTargetingStates $geoTargetingStates,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->geoTargetingStates = $geoTargetingStates;
        $this->config = $config;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $value = $element->getEscapedValue();
        $extraParams = ' multiple';

        if (! $this->config->isConfiguredForUse()) {
            $extraParams .= ' readonly="readonly"';
            $value = LocationsListInterface::ALL;
        }

        /** @var HtmlSelect $select */
        $select = $this->getLayout()->createBlock(HtmlSelect::class);
        $optionArray = $this->geoTargetingStates->toOptionArray();

        $select->setOptions($optionArray)
            ->setId($element->getId())
            ->setName($element->getName())
            ->setValue(explode(',', $value))
            ->setExtraParams($extraParams)
            ->setClass($element->getClass());

        return $select->toHtml();
    }
}
