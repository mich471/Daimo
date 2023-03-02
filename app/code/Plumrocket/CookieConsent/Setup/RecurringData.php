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

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Plumrocket\CookieConsent\Api\CookieRepositoryInterface;
use Plumrocket\CookieConsent\Api\Data\CookieInterface;
use Plumrocket\CookieConsent\Api\Data\CookieInterfaceFactory;
use Plumrocket\CookieConsent\Api\KnownCookieRegistryInterface;
use Plumrocket\CookieConsent\Model\Cookie\Attribute\Source\Type;
use Plumrocket\CookieConsent\Model\ResourceModel\Cookie\GetNames;

/**
 * @since 1.0.0
 */
class RecurringData implements InstallDataInterface
{
    /**
     * @var \Plumrocket\CookieConsent\Model\ResourceModel\Cookie\GetNames
     */
    private $getNames;

    /**
     * @var \Plumrocket\CookieConsent\Api\KnownCookieRegistryInterface
     */
    private $knownCookieRegistry;

    /**
     * @var \Plumrocket\CookieConsent\Api\Data\CookieInterfaceFactory
     */
    private $cookieFactory;

    /**
     * @var \Plumrocket\CookieConsent\Api\CookieRepositoryInterface
     */
    private $cookieRepository;

    /**
     * RecurringData constructor.
     *
     * @param \Plumrocket\CookieConsent\Model\ResourceModel\Cookie\GetNames $getNames
     * @param \Plumrocket\CookieConsent\Api\KnownCookieRegistryInterface    $knownCookieRegistry
     * @param \Plumrocket\CookieConsent\Api\Data\CookieInterfaceFactory     $cookieFactory
     * @param \Plumrocket\CookieConsent\Api\CookieRepositoryInterface       $cookieRepository
     */
    public function __construct(
        GetNames $getNames,
        KnownCookieRegistryInterface $knownCookieRegistry,
        CookieInterfaceFactory $cookieFactory,
        CookieRepositoryInterface $cookieRepository
    ) {
        $this->getNames = $getNames;
        $this->knownCookieRegistry = $knownCookieRegistry;
        $this->cookieFactory = $cookieFactory;
        $this->cookieRepository = $cookieRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $describedCookies = $this->getNames->execute();

        $knownCookies = $this->knownCookieRegistry->getList();

        if ($knownCookies) {
            foreach ($knownCookies as $cookieName => $cookieData) {
                if (in_array($cookieName, $describedCookies, true)) {
                    continue;
                }

                /** @var \Plumrocket\CookieConsent\Api\Data\CookieInterface $cookie */
                $cookie = $this->cookieFactory->create();
                $cookie->setName($cookieName);
                $cookie->setCategoryKey((string) $cookieData[CookieInterface::CATEGORY_KEY]);
                $cookie->setType($cookieData[CookieInterface::TYPE] ?? Type::TYPE_FIRST);
                $cookie->setDomain($cookieData[CookieInterface::DOMAIN] ?? '');
                $cookie->setDuration((int) ($cookieData[CookieInterface::DURATION] ?? 0));
                $cookie->setDescription($cookieData[CookieInterface::DESCRIPTION] ?? '');
                $cookie->setStoreId(0); // save on default level

                $this->cookieRepository->save($cookie);
            }
        }
    }
}
