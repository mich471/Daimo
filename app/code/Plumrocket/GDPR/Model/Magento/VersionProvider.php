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

namespace Plumrocket\GDPR\Model\Magento;

class VersionProvider
{
    const CACHE_IDENTIFIER = 'PR_MAGENTO_VERSION';

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    private $cache;

    /**
     * @var string
     */
    private $magentoVersionLocalCache;

    /**
     * VersionProvider constructor.
     *
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\App\CacheInterface           $cache
     */
    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\App\CacheInterface $cache
    ) {
        $this->productMetadata = $productMetadata;
        $this->cache = $cache;
    }

    /**
     * @param $version
     * @return bool
     */
    public function isMagentoVersionBelow($version)
    {
        return -1 === version_compare($this->getMagentoVersion(), $version);
    }

    /**
     * @return string
     */
    public function getMagentoVersion()
    {
        if (! $this->magentoVersionLocalCache) {
            $magentoVersion = $this->cache->load(self::CACHE_IDENTIFIER);

            if (! $magentoVersion) {
                $magentoVersion = $this->productMetadata->getVersion();

                $this->cache->save(
                    $magentoVersion,
                    self::CACHE_IDENTIFIER,
                    [\Magento\Framework\App\Config::CACHE_TAG]
                );
            }

            $this->magentoVersionLocalCache = $magentoVersion;
        }

        return $this->magentoVersionLocalCache;
    }
}
