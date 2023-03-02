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
use Magento\Backend\Model\UrlInterface;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Element\Html\Select as HtmlSelect;
use Plumrocket\GeoIPLookup\Api\LocationsListInterface;
use Plumrocket\GeoIPLookup\Helper\Config;
use Plumrocket\GeoIPLookup\Model\Base\Information;
use Plumrocket\GeoIPLookup\Model\System\Config\Source\GeoTargeting;

/**
 * @since 1.2.2
 */
class GeoTargetingMultiSelect extends Field
{
    /**
     * @var \Plumrocket\GeoIPLookup\Model\System\Config\Source\GeoTargeting
     */
    private $geoIPRestrictions;

    /**
     * @var \Plumrocket\GeoIPLookup\Helper\Config
     */
    private $config;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    private $backendUrl;

    /**
     * @param \Magento\Backend\Block\Template\Context                         $context
     * @param \Plumrocket\GeoIPLookup\Model\System\Config\Source\GeoTargeting $geoIPRestrictions
     * @param \Plumrocket\GeoIPLookup\Helper\Config                           $config
     * @param \Magento\Backend\Model\UrlInterface                             $backendUrl
     * @param array                                                           $data
     */
    public function __construct(
        Context $context,
        GeoTargeting $geoIPRestrictions,
        Config $config,
        UrlInterface $backendUrl,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->geoIPRestrictions = $geoIPRestrictions;
        $this->config = $config;
        $this->backendUrl = $backendUrl;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = $this->getGeoIpRestrictionsNotice();
        $value = $element->getEscapedValue();
        $extraParams = ' multiple';

        if (! $this->config->isConfiguredForUse()) {
            $extraParams .= ' readonly="readonly"';
            $value = LocationsListInterface::ALL;
        }

        /** @var HtmlSelect $select */
        $select = $this->getLayout()->createBlock(HtmlSelect::class);
        $optionArray = $this->geoIPRestrictions->toOptionArray();

        $select->setOptions($optionArray)
            ->setId($element->getId())
            ->setName($element->getName())
            ->setValue(explode(',', $value))
            ->setExtraParams($extraParams)
            ->setClass($element->getClass());

        $html .= $select->toHtml();

        return $html;
    }

    public function getGeoIpRestrictionsNotice($withContainer = true)
    {
        $href = $this->backendUrl->getUrl('adminhtml/system_config/edit', [
            'section' => Information::CONFIG_SECTION,
        ]);

        $message = '';

        switch (true) {
            case ! $this->config->isModuleEnabled():
                $message = __(
                    'The GDPR Geo Targeting is disabled. Click <a target="_blank" href="%1">here</a> to open ' .
                    'Plumrocket GeoIP Lookup configuration and enable the GeoIP extension.',
                    $href
                );
                break;

            case ! $this->config->getEnableMethodsNumber():
                $message = __(
                    'Please enable at least one GeoIP Lookup database in order to use GDPR Geo Targeting. ' .
                    'Click <a target="_blank" href="%1">here</a> to open Plumrocket GeoIP Lookup configuration ' .
                    'and enable the GeoIP databases.',
                    $href
                );
                break;
        }

        return (bool) $withContainer && ! empty($message)
            ? $this->getPreparedNoticeHtml($message)
            : $message;
    }

    /**
     * @param $message
     * @return string
     */
    public function getPreparedNoticeHtml($message): string
    {
        $style = 'style="margin: 25px 5px;color: #eb5202;background: none;font-size:14px"';

        return "<div $style><div><i>$message</i></div></div>";
    }
}
