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

namespace Plumrocket\CookieConsent\Ui\DataProvider\Cookie\Item\Form\Modifier;

use Magento\Catalog\Model\Attribute\ScopeOverriddenValue;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\Mapper\FormElement;
use Plumrocket\CookieConsent\Api\Data\CookieInterface;
use Plumrocket\CookieConsent\Model\ResourceModel\AbstractAttributeCollection;
use Plumrocket\CookieConsent\Model\ResourceModel\Cookie;
use Plumrocket\CookieConsent\Model\ResourceModel\Cookie\Attribute\CollectionFactory;
use Plumrocket\CookieConsent\Ui\DataProvider\AbstractForm\EavModifier as AbstractFormEavModifier;
use Plumrocket\CookieConsent\Ui\Locator\LocatorInterface;

/**
 * @since 1.0.0
 */
class Eav extends AbstractFormEavModifier
{
    const DATA_SOURCE_DEFAULT = 'cookie_item';
    const DATA_PERSISTOR_KEY = CookieInterface::DATA_PERSISTOR_KEY;
    const ENTITY = CookieInterface::class;

    /**
     * @var \Plumrocket\CookieConsent\Model\ResourceModel\Cookie\Attribute\CollectionFactory
     */
    private $attributesCollectionFactory;

    /**
     * Eav constructor.
     *
     * @param \Magento\Framework\App\ResourceConnection                 $resource
     * @param \Plumrocket\CookieConsent\Ui\Locator\LocatorInterface     $locator
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager
     * @param \Magento\Ui\DataProvider\Mapper\FormElement               $formElementMapper
     * @param \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $eavAttributeFactory
     * @param \Magento\Framework\Stdlib\ArrayManager                    $arrayManager
     * @param \Magento\Catalog\Model\Attribute\ScopeOverriddenValue     $scopeOverriddenValue
     * @param \Magento\Framework\App\Request\DataPersistorInterface     $dataPersistor
     * @param \Plumrocket\CookieConsent\Model\ResourceModel\Cookie      $modelResource
     * @param CollectionFactory                                         $attributesCollectionFactory
     * @param array                                                     $attributesToDisable
     * @param array                                                     $attributesToEliminate
     */
    public function __construct(
        ResourceConnection $resource,
        LocatorInterface $locator,
        StoreManagerInterface $storeManager,
        FormElement $formElementMapper,
        AttributeFactory $eavAttributeFactory,
        ArrayManager $arrayManager,
        ScopeOverriddenValue $scopeOverriddenValue,
        DataPersistorInterface $dataPersistor,
        Cookie $modelResource,
        CollectionFactory $attributesCollectionFactory,
        $attributesToDisable = [],
        $attributesToEliminate = []
    ) {
        parent::__construct(
            $resource,
            $locator,
            $storeManager,
            $formElementMapper,
            $eavAttributeFactory,
            $arrayManager,
            $scopeOverriddenValue,
            $dataPersistor,
            $modelResource,
            $attributesToDisable,
            $attributesToEliminate
        );
        $this->attributesCollectionFactory = $attributesCollectionFactory;
    }

    /**
     * @return \Plumrocket\CookieConsent\Model\ResourceModel\AbstractAttributeCollection
     */
    protected function getAttributesCollection(): AbstractAttributeCollection
    {
        return $this->attributesCollectionFactory->create();
    }
}
