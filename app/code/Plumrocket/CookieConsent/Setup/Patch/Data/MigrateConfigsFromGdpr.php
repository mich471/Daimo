<?php
/**
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Setup\Patch\Data;

use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Plumrocket\CookieConsent\Helper\Config\CookieNotice as CookieNoticeConfig;
use Plumrocket\CookieConsent\Helper\Config\SettingsBar as SettingsBarConfig;

/**
 * @since 1.3.0
 */
class MigrateConfigsFromGdpr implements DataPatchInterface
{
    /**
     * @var \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory
     */
    private $configDataCollectionFactory;

    /**
     * @param \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory $configDataCollectionFactory
     */
    public function __construct(
        CollectionFactory $configDataCollectionFactory
    ) {
        $this->configDataCollectionFactory = $configDataCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        /** @var \Magento\Config\Model\ResourceModel\Config\Data\Collection $configDataCollection */
        $configDataCollection = $this->configDataCollectionFactory->create();
        $configDataCollection->addPathFilter('prgdpr/cookie_consent');

        $mapping = [
            'prgdpr/cookie_consent/geoip_restriction' => 'pr_cookie/main_settings/geo_targeting',
            'prgdpr/cookie_consent/geoip_restriction_usa_ccpa' => 'pr_cookie/main_settings/geoip_restriction_usa_ccpa',
            'prgdpr/cookie_consent/notice_text' => CookieNoticeConfig::XML_PATH_TEXT,
            'prgdpr/cookie_consent/button_label' => [
                CookieNoticeConfig::XML_PATH_ACCEPT_BUTTON_GROUP . '/label',
                SettingsBarConfig::XML_PATH_ACCEPT_BUTTON_GROUP . '/label',
            ],
            'prgdpr/cookie_consent/decline_button_label' => [
                CookieNoticeConfig::XML_PATH_DECLINE_BUTTON_GROUP . '/label',
                SettingsBarConfig::XML_PATH_DECLINE_BUTTON_GROUP . '/label',
            ],
        ];

        /** @var \Magento\Framework\App\Config\Value $config */
        foreach ($configDataCollection as $config) {
            $oldPath = $config->getData('path');
            if (! array_key_exists($oldPath, $mapping)) {
                continue;
            }

            if (is_array($mapping[$oldPath])) {
                $config->setData('path', $mapping[$oldPath][0]);
                foreach ($mapping[$oldPath] as $key => $path) {
                    if ($key === 0) {
                        continue;
                    }
                    $relatedConfig = $configDataCollection->getNewEmptyItem();
                    $relatedConfig->setData($config->getData());
                    $relatedConfig->setId(null);
                    $relatedConfig->setData('path', $path);
                    $configDataCollection->addItem($relatedConfig);
                }
            } else {
                $config->setData('path', $mapping[$oldPath]);
            }
        }

        $configDataCollection->save();
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
