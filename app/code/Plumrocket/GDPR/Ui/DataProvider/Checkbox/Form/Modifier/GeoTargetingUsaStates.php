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

class GeoTargetingUsaStates implements ModifierInterface
{
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
        if (isset($meta['general']['children']['geo_targeting_usa_states'])) {
            $geoTargetingConfig
                = $meta['general']['children']['geo_targeting_usa_states']['arguments']['data']['config'];
            $geoTargetingConfig['component'] = 'Plumrocket_GDPR/js/form/element/extended-multiselect-states';

            $meta['general']['children']['geo_targeting_usa_states']['arguments']['data']['config']
                = $geoTargetingConfig;
        }

        return $meta;
    }
}
