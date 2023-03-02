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
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GeoIPLookup\Helper;

use Plumrocket\GeoIPLookup\Model\Base\Information;

/**
 * Class Data Helper
 */
class Data extends \Plumrocket\GeoIPLookup\Helper\Main
{
    /**
     * Config section id
     * @deprecated since 1.2.2
     * @see Information::CONFIG_SECTION
     */
    const SECTION_ID = 'prgeoiplookup';

    /**
     * @var string
     */
    protected $_configSectionId = Information::CONFIG_SECTION;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $timezone;

    /**
     * @var \Plumrocket\GeoIPLookup\Helper\Config
     */
    private $config;

    /**
     * @param \Magento\Framework\ObjectManagerInterface            $objectManager
     * @param \Magento\Framework\App\Helper\Context                $context
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Plumrocket\GeoIPLookup\Helper\Config                $config
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Plumrocket\GeoIPLookup\Helper\Config $config
    ) {
        parent::__construct($objectManager, $context);
        $this->timezone = $timezone;
        $this->config = $config;
    }

    /**
     * @deplacated since 1.2.3
     * @see \Plumrocket\GeoIPLookup\Helper\Config::isModuleEnabled
     * @param null $store
     * @return bool
     */
    public function moduleEnabled($store = null)
    {
        return $this->config->isModuleEnabled($store);
    }

    /**
     * @param $elementId
     * @return mixed
     */
    public function getModelNameByElementId($elementId, $upercase = true, $penultimate = false)
    {
        if ($penultimate) {
            $elementId = mb_substr($elementId, 0, mb_strrpos($elementId, "_"));
        }
        $elements = explode('_', $elementId);
        $modelName = end($elements);
        if ($upercase) {
            $modelName = ucfirst($modelName);
        }

        return $modelName;
    }

    /**
     * @param $version
     * @return \Magento\Framework\Phrase
     */
    public function formatInstalledVersion($version)
    {
        $formattedVersion = __('Not Installed');
        if ($version) {
            $installedDate = $this->timezone->date($version['installed_date'])->format("F d, Y");
            if ($version['file_version']) {
                $formattedVersion = __("Installed v%1 on %2", $version['file_version'], $installedDate);
            } else {
                $formattedVersion = __("Installed on %1", $version);
            }
        }

        return $formattedVersion;
    }
}
