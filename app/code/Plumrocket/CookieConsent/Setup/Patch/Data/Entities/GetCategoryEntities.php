<?php
/**
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Setup\Patch\Data\Entities;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Plumrocket\CookieConsent\Api\Data\CategoryInterface;
use Plumrocket\CookieConsent\Model\Category;
use Plumrocket\CookieConsent\Model\Eav\Attribute;
use Plumrocket\CookieConsent\Model\ResourceModel\Category\Attribute\Collection;

/**
 * Get category entities data
 *
 * @since 1.3.0
 */
class GetCategoryEntities
{
    /**
     * @return array[]
     */
    public function execute(): array
    {
        $categoryEntity = Category::ENTITY;

        return [
            $categoryEntity => [
                'entity_model' => \Plumrocket\CookieConsent\Model\ResourceModel\Category::class,
                'attribute_model' => Attribute::class,
                'table' => \Plumrocket\CookieConsent\Model\ResourceModel\Category::MAIN_TABLE_NAME,
                'additional_attribute_table' => Collection::EAV_ATTRIBUTE_ADDITIONAL_TABLE,
                'entity_attribute_collection' => Collection::class,
                'attributes' => [
                    CategoryInterface::STATUS => [
                        'type' => 'static',
                        'label' => 'Is Enabled?',
                        'required' => false,
                        'default' => 1,
                        'input' => 'boolean',
                        'source' => Boolean::class,
                        'sort_order' => 100,
                        'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                        'group' => 'General Information',
                        'note' => 'Indicates whether the category is visible on frontend.',
                    ],
                    CategoryInterface::IS_ESSENTIAL => [
                        'type' => 'static',
                        'label' => 'Is Essential?',
                        'required' => false,
                        'default' => 0,
                        'input' => 'boolean',
                        'source' => Boolean::class,
                        'sort_order' => 200,
                        'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                        'group' => 'General Information',
                        'note' => 'If the cookie category is defined as "essential" then users will not be able to ' .
                            'disable it in the frontend. An example of essential cookies would be session cookies, ' .
                            'persistent cookies, shopping cart cookies, etc.',
                    ],
                    CategoryInterface::NAME => [
                        'type' => 'text',
                        'label' => 'Name',
                        'required' => true,
                        'default' => '',
                        'input' => 'text',
                        'sort_order' => 300,
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                        'group' => 'General Information',
                    ],
                    CategoryInterface::KEY => [
                        'type' => 'static',
                        'label' => 'Category Identifier',
                        'required' => true,
                        'input' => 'text',
                        'sort_order' => 400,
                        'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                        'group' => 'General Information',
                    ],
                    CategoryInterface::DESCRIPTION => [
                        'type' => 'text',
                        'label' => 'Description',
                        'required' => false,
                        'default' => '',
                        'input' => 'textarea',
                        'sort_order' => 500,
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                        'group' => 'General Information',
                    ],
                    CategoryInterface::SORT_ORDER => [
                        'type' => 'int',
                        'label' => 'Sort Order',
                        'required' => false,
                        'default' => 0,
                        'input' => 'text',
                        'sort_order' => 600,
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                        'group' => 'General Information',
                        'note' => 'Sort order is a category position in "Cookie Settings Panel".',
                    ],
                    CategoryInterface::HEAD_SCRIPTS => [
                        'type' => 'text',
                        'label' => 'Head Scripts',
                        'required' => false,
                        'default' => '',
                        'input' => 'textarea',
                        'sort_order' => 700,
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                        'group' => 'General Information',
                        'note' => 'Scripts and HTML listed here will be included right after the opening <body> tag. ' .
                            'They will be executed if the corresponding cookie category is enabled by the customer.'
                    ],
                    CategoryInterface::FOOTER_MISCELLANEOUS_HTML => [
                        'type' => 'text',
                        'label' => 'Footer Miscellaneous HTML',
                        'required' => false,
                        'default' => '',
                        'input' => 'textarea',
                        'sort_order' => 800,
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                        'group' => 'General Information',
                        'note' => 'Scripts and HTML listed here will be included right before the closing </body> ' .
                            'tag. They will be executed if the corresponding cookie category is enabled by the ' .
                            'customer.',
                    ],
                    CategoryInterface::IS_PRE_CHECKED => [
                        'type' => 'int',
                        'label' => 'Is Pre-checked?',
                        'required' => false,
                        'default' => 0,
                        'input' => 'boolean',
                        'sort_order' => 250,
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                        'group' => 'General Information',
                        'note' => 'Indicates whether the category switcher is "ON" by default' .
                            ' in the Cookie Settings Panel. Please note, the cookies from this category will be accepted' .
                            ' only after the visitor confirms the choices.',
                    ]
                ],
            ],
        ];
    }
}
