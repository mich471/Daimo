<?php
/**
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Setup\Patch\Data\Entities;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Plumrocket\CookieConsent\Api\Data\CookieInterface;
use Plumrocket\CookieConsent\Model\Cookie;
use Plumrocket\CookieConsent\Model\Cookie\Attribute\Source\CategoryKey;
use Plumrocket\CookieConsent\Model\Cookie\Attribute\Source\Type as CookieType;
use Plumrocket\CookieConsent\Model\Eav\Attribute;
use Plumrocket\CookieConsent\Model\ResourceModel\Cookie\Attribute\Collection;

/**
 * Get cookie entities data
 *
 * @since 1.3.0
 */
class GetCookieEntities
{
    /**
     * @return array[]
     */
    public function execute(): array
    {
        $categoryEntity = Cookie::ENTITY;

        return [
            $categoryEntity => [
                'entity_model' => \Plumrocket\CookieConsent\Model\ResourceModel\Cookie::class,
                'attribute_model' => Attribute::class,
                'table' => \Plumrocket\CookieConsent\Model\ResourceModel\Cookie::MAIN_TABLE_NAME,
                'additional_attribute_table' => Collection::EAV_ATTRIBUTE_ADDITIONAL_TABLE,
                'entity_attribute_collection' => Collection::class,
                'attributes' => [
                    CookieInterface::NAME => [
                        'type' => 'static',
                        'label' => 'Name',
                        'required' => true,
                        'default' => '',
                        'input' => 'text',
                        'sort_order' => 100,
                        'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                        'group' => 'General Information',
                    ],
                    CookieInterface::TYPE => [
                        'type' => 'static',
                        'label' => 'Type',
                        'required' => true,
                        'input' => 'select',
                        'source' => CookieType::class,
                        'sort_order' => 200,
                        'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                        'group' => 'General Information',
                        'note' => 'A first-party cookie is set by your Magento store. A third-party cookie is set by' .
                            ' a third-party service, via code loaded on your website.',
                    ],
                    CookieInterface::DOMAIN => [
                        'type' => 'static',
                        'label' => 'Domain',
                        'required' => true,
                        'default' => '',
                        'input' => 'text',
                        'sort_order' => 300,
                        'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                        'group' => 'General Information',
                        'note' => 'The domain for which the cookie is valid. If this attribute is not specified, ' .
                            'then the Magento base URL is used as the default value.',
                    ],
                    CookieInterface::DURATION => [
                        'type' => 'static',
                        'label' => 'Duration',
                        'required' => true,
                        'default' => 0,
                        'input' => 'text',
                        'sort_order' => 400,
                        'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                        'group' => 'General Information',
                        'note' => 'Set the period when the cookie should expire. If set to 0, or omitted, the cookie ' .
                            'will expire at the end of the session (when the browser closes).',
                    ],
                    CookieInterface::DESCRIPTION => [
                        'type' => 'text',
                        'label' => 'Description',
                        'required' => false,
                        'default' => '',
                        'input' => 'textarea',
                        'sort_order' => 500,
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                        'group' => 'General Information',
                    ],
                    CookieInterface::CATEGORY_KEY => [
                        'type' => 'static',
                        'label' => 'Cookie Category',
                        'required' => true,
                        'input' => 'select',
                        'source' => CategoryKey::class,
                        'sort_order' => 600,
                        'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                        'group' => 'General Information',
                    ],
                ],
            ],
        ];
    }
}
