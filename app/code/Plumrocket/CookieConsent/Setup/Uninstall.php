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

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Setup;

use Plumrocket\Base\Setup\AbstractUninstall;
use Plumrocket\CookieConsent\Api\Data\CategoryInterface;
use Plumrocket\CookieConsent\Api\Data\CookieInterface;
use Plumrocket\CookieConsent\Model\Category;
use Plumrocket\CookieConsent\Model\Cookie;
use Plumrocket\CookieConsent\Model\ResourceModel\AbstractAttributeCollection;
use Plumrocket\CookieConsent\Model\ResourceModel\Category as CategoryResource;
use Plumrocket\CookieConsent\Model\ResourceModel\ConsentLog as ConsentLogResource;
use Plumrocket\CookieConsent\Model\ResourceModel\Cookie as CookieResource;

/**
 * Uninstall Plumrocket_CookieConsent
 */
class Uninstall extends AbstractUninstall
{
    /**
     * Config section id
     *
     * @var string
     */
    protected $_configSectionId = 'pr_cookie';

    /**
     * Patches to files
     *
     * @var array
     */
    protected $_pathes = ['/app/code/Plumrocket/CookieConsent'];

    /**
     * @var array
     */
    protected $_attributes = [
        Cookie::ENTITY => [
            CookieInterface::NAME,
            CookieInterface::TYPE,
            CookieInterface::DOMAIN,
            CookieInterface::DURATION,
            CookieInterface::DESCRIPTION,
            CookieInterface::CATEGORY_KEY,
        ],
        Category::ENTITY => [
            CategoryInterface::STATUS,
            CategoryInterface::IS_ESSENTIAL,
            CategoryInterface::KEY,
            CategoryInterface::NAME,
            CategoryInterface::DESCRIPTION,
            CategoryInterface::SORT_ORDER,
            CategoryInterface::HEAD_SCRIPTS,
            CategoryInterface::FOOTER_MISCELLANEOUS_HTML,
        ],
    ];

    protected $eavEntityTypes = [Cookie::ENTITY, Category::ENTITY];

    /**
     * Tables
     *
     * @var array
     */
    protected $_tables = [
        AbstractAttributeCollection::EAV_ATTRIBUTE_ADDITIONAL_TABLE,

        CookieResource::MAIN_TABLE_NAME,
        CookieResource::MAIN_TABLE_NAME . '_text',

        CategoryResource::MAIN_TABLE_NAME,
        CategoryResource::MAIN_TABLE_NAME . '_int',
        CategoryResource::MAIN_TABLE_NAME . '_text',

        ConsentLogResource::MAIN_TABLE_NAME,
    ];
}
