<?php
/**
 * Plumrocket Inc.
 * NOTICE OF LICENSE
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

namespace Plumrocket\CookieConsent\Model\ResourceModel\Cookie\Grid;

use Plumrocket\CookieConsent\Model\ResourceModel\Cookie\Collection as CookieCollection;
use Magento\Framework\Session\Config\ConfigInterface;
use Magento\Framework\Data\Collection\EntityFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Eav\Model\Config;
use Magento\Framework\App\ResourceConnection;
use Magento\Eav\Model\EntityFactory as EavEntityFactory;
use Magento\Eav\Model\ResourceModel\Helper;
use Magento\Framework\Validator\UniversalFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Plumrocket\CookieConsent\Model\Cookie\Attribute\Source\Type;
use Plumrocket\CookieConsent\Api\Data\CookieInterface;

/**
 * @since 1.0.0
 */
class Collection extends CookieCollection
{
    const DOMAIN_LABEL = 'domain_label';

    /**
     * @var \Magento\Framework\Session\Config\ConfigInterface
     */
    private $sessionConfig;

    /**
     * Collection constructor.
     *
     * @param \Magento\Framework\Data\Collection\EntityFactory             $entityFactory
     * @param \Psr\Log\LoggerInterface                                     $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param \Magento\Eav\Model\Config                                    $eavConfig
     * @param \Magento\Framework\App\ResourceConnection                    $resource
     * @param \Magento\Eav\Model\EntityFactory                             $eavEntityFactory
     * @param \Magento\Eav\Model\ResourceModel\Helper                      $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory                $universalFactory
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
     * @param \Magento\Framework\Session\Config\ConfigInterface            $sessionConfig
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null          $connection
     */
    public function __construct(
        EntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        Config $eavConfig,
        ResourceConnection $resource,
        EavEntityFactory $eavEntityFactory,
        Helper $resourceHelper,
        UniversalFactory $universalFactory,
        StoreManagerInterface $storeManager,
        ConfigInterface $sessionConfig,
        AdapterInterface $connection = null
    ) {
        $this->sessionConfig = $sessionConfig;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $connection
        );
    }

    /**
     * @return $this
     */
    protected function _initSelect(): self
    {
        parent::_initSelect();

        $cookieDomain = $this->sessionConfig->getCookieDomain();
        $domainColumn = CookieInterface::DOMAIN;
        $typeColumn = CookieInterface::TYPE;
        $expression = 'IF(' . $typeColumn . ' = "' . Type::TYPE_FIRST . '" AND isnull(' . $domainColumn . '), "' .
            $cookieDomain . '", ' . $domainColumn .') AS ' . self::DOMAIN_LABEL . ' ';

        $this->getSelect()->columns(new \Zend_Db_Expr($expression));

        return $this;
    }
}
