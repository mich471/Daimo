<?php
/**
 * @package     Plumrocket_magento2.3.6
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @since 3.1.0
 */
class AddClassToBody implements ObserverInterface
{
    /** @var string */
    const THEME_CODE_DEFAULT = 'Magento/blank';

    /** @var \Magento\Framework\View\Page\Config */
    private $pageConfig;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    private $scopeConfig;

    /** @var \Magento\Framework\View\Design\Theme\ThemeProviderInterface */
    private $themeProvider;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;

    /**
     * @param \Magento\Framework\View\Page\Config                         $pageConfig
     * @param \Magento\Framework\App\Config\ScopeConfigInterface          $scopeConfig
     * @param \Magento\Framework\View\Design\Theme\ThemeProviderInterface $themeProvider
     * @param \Magento\Store\Model\StoreManagerInterface                  $storeManager
     */
    public function __construct(
        PageConfig $pageConfig,
        ScopeConfigInterface $scopeConfig,
        ThemeProviderInterface $themeProvider,
        StoreManagerInterface $storeManager
    ) {
        $this->pageConfig = $pageConfig;
        $this->scopeConfig = $scopeConfig;
        $this->themeProvider = $themeProvider;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $themeId = $this->scopeConfig->getValue(
            \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );

        $themeData = $this->themeProvider->getThemeById($themeId)->getData();
        $code = $themeData['code'] ?? self::THEME_CODE_DEFAULT;

        $bodyClassVandor = "prgdpr-" . mb_strstr(mb_strtolower($code), '/', true);
        $bodyClassTheme = "prgdpr-" . str_replace("/", "-", mb_strtolower($code));
        $this->pageConfig->addBodyClass($bodyClassVandor)->addBodyClass($bodyClassTheme);
    }
}
