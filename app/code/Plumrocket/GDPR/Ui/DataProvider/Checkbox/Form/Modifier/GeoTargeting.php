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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Ui\DataProvider\Checkbox\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class GeoTargeting implements ModifierInterface
{
    /**
     * @var \Plumrocket\GDPR\Helper\Geo\Location
     */
    private $geoLocationHelper;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    private $backendUrl;

    /**
     * GeoTargeting constructor.
     *
     * @param \Plumrocket\GDPR\Helper\Geo\Location            $geoLocationHelper
     * @param \Magento\Backend\Model\UrlInterface             $backendUrl
     */
    public function __construct(
        \Plumrocket\GDPR\Helper\Geo\Location $geoLocationHelper,
        \Magento\Backend\Model\UrlInterface $backendUrl
    ) {
        $this->geoLocationHelper = $geoLocationHelper;
        $this->backendUrl = $backendUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        if (isset($meta['general']['children']['geo_targeting'])) {
            $geoTargetingConfig = $meta['general']['children']['geo_targeting']['arguments']['data']['config'];

            $geoTargetingConfig['component'] = 'Plumrocket_GDPR/js/form/element/extended-multiselect';
            $geoTargetingConfig['geoIpMessage'] = $this->getGeoIpMessage();

            $meta['general']['children']['geo_targeting']['arguments']['data']['config'] = $geoTargetingConfig;
        }

        return $meta;
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    private function getGeoIpMessage()
    {
        switch ($this->geoLocationHelper->getGeoIpModuleStatus()) {
            case \Plumrocket\GDPR\Helper\Geo\Location::STATUS_ABSENT:
                return __(
                    'The GDPR Geo Targeting is disabled. You must install ' .
                    'Plumrocket GeoIP Lookup extension to enable this feature.'
                );

            case \Plumrocket\GDPR\Helper\Geo\Location::STATUS_INSTALLED:
                return __(
                    'The GDPR Geo Targeting is disabled. Click <a href="%1" target="_blank">here</a> to open ' .
                    'Plumrocket GeoIP Lookup configuration and enable the GeoIP extension.',
                    $this->backendUrl->getUrl('adminhtml/system_config/edit', ['section' => 'prgdpr'])
                );

            case \Plumrocket\GDPR\Helper\Geo\Location::STATUS_NOT_CONFIGURED:
                return __(
                    'Please enable at least one GeoIP Lookup database in order to use GDPR Geo Targeting. Click ' .
                    '<a href="%1" target="_blank">here</a> to open Plumrocket GeoIP Lookup configuration and ena' .
                    'ble the GeoIP databases. ',
                    $this->backendUrl->getUrl('adminhtml/system_config/edit', ['section' => 'prgdpr'])
                );
            default:
                return '';
        }
    }
}
