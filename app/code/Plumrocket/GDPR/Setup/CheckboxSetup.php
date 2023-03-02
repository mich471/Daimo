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

namespace Plumrocket\GDPR\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Plumrocket\GDPR\Model\Checkbox\Attribute\Source\GeoTargeting as GeoTargetingSource;
use Plumrocket\GDPR\Model\ResourceModel\Checkbox\Attribute\Collection as CheckboxAttributeCollection;

class CheckboxSetup extends \Magento\Eav\Setup\EavSetup
{
    public function getDefaultEntities()
    {
        $checkboxEntity = \Plumrocket\GDPR\Model\Checkbox::ENTITY;

        return [
            $checkboxEntity => [
                'entity_model' => \Plumrocket\GDPR\Model\ResourceModel\Checkbox::class,
                'attribute_model' => \Plumrocket\GDPR\Model\Checkbox\Attribute::class,
                'table' => $checkboxEntity . '_entity',
                'additional_attribute_table' => 'catalog_eav_attribute',
                'entity_attribute_collection' => CheckboxAttributeCollection::class,
                'attributes' => [
                    'status' => [
                        'type' => 'int',
                        'label' => 'Display Checkbox',
                        'required' => false,
                        'default' => 1,
                        'input' => 'boolean',
                        'source' => Boolean::class,
                        'sort_order' => 100,
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                        'group' => 'General Information',
                    ],
                    'location_key' => [
                        'type' => 'static',
                        'label' => 'Consent Location',
                        'required' => true,
                        'input' => 'select',
                        'source' => \Plumrocket\GDPR\Model\Config\Source\ConsentLocationsGrouped::class,
                        'sort_order' => 200,
                        'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                        'group' => 'General Information',
                        'note' => 'Please choose location where the consent checkbox must be displayed. Showing' .
                            ' checkbox in "custom location" will require code modifications. Please read our de' .
                            'veloper\'s guide for more info.'
                    ],
                    'label' => [
                        'type' => 'text',
                        'label' => 'Checkbox Label',
                        'required' => false,
                        'default' => 'I agree to <a href="{{url}}" class="pr-inpopup" target="_blank">' .
                            'Privacy Policy' .
                            '</a>',
                        'input' => 'textarea',
                        'sort_order' => 300,
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                        'group' => 'General Information',
                    ],
                    'cms_page_id' => [
                        'type' => 'int',
                        'label' => 'Link to CMS Page',
                        'required' => false,
                        'input' => 'select',
                        'source' => \Plumrocket\GDPR\Model\Checkbox\Attribute\Source\CmsPage::class,
                        'sort_order' => 400,
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                        'group' => 'General Information',
                        'note' => 'Please choose CMS page associated with this checkbox. Linked CMS page content ' .
                            'will be displayed in popup. Leave empty if you dont want to display any CMS content.'
                    ],
                    'require' => [
                        'type' => 'int',
                        'label' => 'Required',
                        'required' => false,
                        'default' => 1,
                        'input' => 'boolean',
                        'source' => Boolean::class,
                        'sort_order' => 500,
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                        'group' => 'General Information',
                        'note' => 'Please indicate if this checkbox must be checked in order to submit a form.'
                    ],
                    'geo_targeting' => [
                        'type' => 'text',
                        'label' => 'Geo Targeting',
                        'required' => false,
                        'default' => 'all',
                        'input' => 'multiselect',
                        'source' => GeoTargetingSource::class,
                        'backend' => \Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend::class,
                        'sort_order' => 600,
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                        'group' => 'General Information',
                        'note' => 'Show consent checkbox only to visitors from the selected list of countries.'
                    ],
                    'internal_note' => [
                        'type' => 'static',
                        'label' => 'Internal Note',
                        'required' => false,
                        'input' => 'textarea',
                        'sort_order' => 700,
                        'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                        'group' => 'General Information',
                        'note' => 'Optional field for Admin users only. Useful to describe ' .
                            'custom checkbox location (such as "New Year\'s promo landing p' .
                            'age") or add some internal notes.'
                    ],
                ],
            ],
        ];
    }
}
