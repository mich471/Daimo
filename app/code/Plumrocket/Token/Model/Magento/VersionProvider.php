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
 * @package     Plumrocket_Token
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */
declare(strict_types=1);

namespace Plumrocket\Token\Model\Magento;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class VersionProvider
 *
 * @since 1.0.4
 * @deprecated will be removed after left support of magento 2.1.*
 * TODO: remove in next release
 */
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
        ProductMetadataInterface $productMetadata,
        CacheInterface $cache
    ) {
        $this->productMetadata = $productMetadata;
        $this->cache = $cache;
    }

    /**
     * @param $version
     * @return bool
     */
    public function isMagentoVersionBelow($version) : bool
    {
        return -1 === version_compare($this->getMagentoVersion(), $version);
    }

    /**
     * @return string
     */
    public function getMagentoVersion() : string
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
