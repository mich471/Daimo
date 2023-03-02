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

namespace Plumrocket\CookieConsent\Ui\DataProvider\AbstractForm;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Attribute\ScopeOverriddenValue;
use Magento\Catalog\Model\ResourceModel\AbstractResource as CatalogAbstractResource;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as EavAttribute;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier as ProductAbstractModifier;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\DataProvider\Mapper\FormElement;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Plumrocket\CookieConsent\Model\Eav\Attribute as CookieConsentEavAttribute;
use Plumrocket\CookieConsent\Model\ResourceModel\AbstractAttributeCollection;
use Plumrocket\CookieConsent\Ui\Locator\LocatorInterface;

abstract class EavModifier implements ModifierInterface
{
    const DATA_SOURCE_DEFAULT = '';
    const DATA_PERSISTOR_KEY = '';
    const ENTITY = '';

    /**
     * @var \Plumrocket\CookieConsent\Ui\Locator\LocatorInterface
     */
    private $locator;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Ui\DataProvider\Mapper\FormElement
     */
    private $formElementMapper;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory
     */
    private $eavAttributeFactory;

    /**
     * @var \Magento\Framework\Stdlib\ArrayManager
     */
    private $arrayManager;

    /**
     * @var \Magento\Catalog\Model\Attribute\ScopeOverriddenValue
     */
    private $scopeOverriddenValue;

    /**
     * @var array
     */
    private $attributesToDisable;

    /**
     * @var array
     */
    private $attributesToEliminate;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var EavAttribute[]
     */
    private $attributes = [];

    /**
     * @var array
     */
    private $canDisplayUseDefault = [];

    /**
     * internal cache for attribute models
     * @var array
     */
    private $attributesCache = [];

    /**
     * @var CatalogAbstractResource
     */
    private $modelResource;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

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
     * @param \Magento\Catalog\Model\ResourceModel\AbstractResource     $modelResource
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
        CatalogAbstractResource $modelResource,
        $attributesToDisable = [],
        $attributesToEliminate = []
    ) {
        $this->resource = $resource;
        $this->locator = $locator;
        $this->storeManager = $storeManager;
        $this->formElementMapper = $formElementMapper;
        $this->eavAttributeFactory = $eavAttributeFactory;
        $this->arrayManager = $arrayManager;
        $this->scopeOverriddenValue = $scopeOverriddenValue;
        $this->dataPersistor = $dataPersistor;
        $this->attributesToDisable = $attributesToDisable;
        $this->attributesToEliminate = $attributesToEliminate;
        $this->modelResource = $modelResource;
    }

    /**
     * @return \Plumrocket\CookieConsent\Model\ResourceModel\AbstractAttributeCollection
     */
    abstract protected function getAttributesCollection(): AbstractAttributeCollection;

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $attributes = $this->getAttributes();
        if ($attributes) {
            $meta['general']['children'] = $this->getAttributesMeta($attributes);
        }

        return $meta;
    }

    /**
     * Get attributes meta
     *
     * @param CookieConsentEavAttribute[] $attributes
     * @return array
     */
    private function getAttributesMeta(array $attributes): array
    {
        $meta = [];

        foreach ($attributes as $sortOrder => $attribute) {
            if (in_array($attribute->getAttributeCode(), $this->attributesToEliminate, true)) {
                continue;
            }

            $meta[$attribute->getAttributeCode()] = $this->setupAttributeMeta($attribute, $attribute->getSortOrder());
        }

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        if ($this->dataPersistor->get(static::DATA_PERSISTOR_KEY) && ! $this->locator->getModel()->getId()) {
            return $this->resolvePersistentData($data);
        }

        $modelId = $this->locator->getModel()->getId();

        $attributes = $this->getAttributes();

        foreach ($attributes as $attribute) {
            if (null !== ($attributeValue = $this->setupAttributeData($attribute))) {
                $data[$modelId][$attribute->getAttributeCode()] = $attributeValue;
            }
        }

        return $data;
    }

    /**
     * Resolve data persistence
     *
     * @param array $data
     * @return array
     */
    private function resolvePersistentData(array $data): array
    {
        $persistentData = (array) $this->dataPersistor->get(static::DATA_PERSISTOR_KEY);
        $this->dataPersistor->clear(static::DATA_PERSISTOR_KEY);
        $modelId = $this->locator->getModel()->getId();

        if (empty($data[$modelId][static::DATA_SOURCE_DEFAULT])) {
            $data[$modelId][static::DATA_SOURCE_DEFAULT] = [];
        }

        $data[$modelId] = array_replace_recursive(
            $data[$modelId][static::DATA_SOURCE_DEFAULT],
            $persistentData
        );

        return $data;
    }

    /**
     * Retrieve attributes
     *
     * @return CookieConsentEavAttribute[]
     */
    private function getAttributes(): array
    {
        if (! $this->attributes) {
            $collection = $this->getAttributesCollection();
            $eavEntityAttributeTbl = $this->resource->getTableName('eav_entity_attribute');

            $collection->setEntityTypeFilter($this->modelResource->getTypeId())
                ->join(
                    [$eavEntityAttributeTbl],
                    'main_table.attribute_id = ' . $eavEntityAttributeTbl . '.attribute_id',
                    ['sort_order']
                )->setOrder('sort_order', 'ASC');

            $this->attributes = $collection->load()->getItems();
        }

        return $this->attributes;
    }

    /**
     * Check is model already exists or we trying to create one
     *
     * @return bool
     */
    private function isModelExists(): bool
    {
        try {
            return (bool) $this->locator->getModel()->getId();
        } catch (NotFoundException $e) {
            return false;
        }
    }

    /**
     * @param CookieConsentEavAttribute $attribute
     * @param           $sortOrder
     * @return array
     */
    public function setupAttributeMeta(CookieConsentEavAttribute $attribute, $sortOrder): array
    {
        $configPath = ltrim(ProductAbstractModifier::META_CONFIG_PATH, ArrayManager::DEFAULT_PATH_DELIMITER);
        $attributeCode = $attribute->getAttributeCode();
        $meta = $this->arrayManager->set($configPath, [], [
            'dataType' => $attribute->getFrontendInput(),
            'formElement' => $this->getFormElementsMapValue($attribute->getFrontendInput()),
            'visible' => $attribute->getIsVisible(),
            'required' => $attribute->getIsRequired(),
            'notice' => $attribute->getNote() === null ? null : __($attribute->getNote()),
            'default' => (! $this->isModelExists()) ? $attribute->getDefaultValue() : null,
            'label' => __($attribute->getDefaultFrontendLabel()),
            'code' => $attributeCode,
            'source' => static::DATA_SOURCE_DEFAULT,
            'scopeLabel' => $this->getScopeLabel($attribute),
            'globalScope' => $this->isScopeGlobal($attribute),
            'sortOrder' => $sortOrder,
        ]);

        $attributeModel = $this->getAttributeModel($attribute);
        if ($attributeModel->usesSource()) {
            $options = $attributeModel->getSource()->getAllOptions();
            $meta = $this->arrayManager->merge($configPath, $meta, [
                'options' => $this->convertOptionsValueToString($options),
            ]);
        }

        if ($this->canDisplayUseDefault($attribute)) {
            $meta = $this->arrayManager->merge($configPath, $meta, [
                'service' => [
                    'template' => 'ui/form/element/helper/service',
                ]
            ]);
        }

        if (!$this->arrayManager->exists($configPath . '/componentType', $meta)) {
            $meta = $this->arrayManager->merge($configPath, $meta, [
                'componentType' => Field::NAME,
            ]);
        }

        $model = $this->locator->getModel();
        if (in_array($attributeCode, $this->attributesToDisable, true)
            || $model->isLockedAttribute($attributeCode)) {
            $meta = $this->arrayManager->merge($configPath, $meta, [
                'disabled' => true,
            ]);
        }

        $childData = $this->arrayManager->get($configPath, $meta, []);
        if (! empty($childData['required'])) {
            $meta = $this->arrayManager->merge($configPath, $meta, [
                'validation' => ['required-entry' => true],
            ]);
        }

        $meta = $this->addUseDefaultValueCheckbox($attribute, $meta);

        if ('boolean' === $attribute->getFrontendInput()) {
            $meta = $this->customizeCheckbox($attribute, $meta);
        }

        return $meta;
    }

    /**
     * Convert options value to string.
     *
     * @param array $options
     * @return array
     */
    private function convertOptionsValueToString(array $options): array
    {
        array_walk($options, static function (&$value) {
            if (isset($value['value']) && is_scalar($value['value'])) {
                $value['value'] = (string) $value['value'];
            }
        });

        return $options;
    }

    /**
     * Adds 'use default value' checkbox.
     *
     * @param CookieConsentEavAttribute $attribute
     * @param array                     $meta
     * @return array
     */
    private function addUseDefaultValueCheckbox(CookieConsentEavAttribute $attribute, array $meta): array
    {
        $canDisplayService = $this->canDisplayUseDefault($attribute);
        if ($canDisplayService) {
            $meta['arguments']['data']['config']['service'] = [
                'template' => 'ui/form/element/helper/service',
            ];

            $meta['arguments']['data']['config']['disabled'] = ! $this->scopeOverriddenValue->containsValue(
                static::ENTITY,
                $this->locator->getModel(),
                $attribute->getAttributeCode(),
                $this->locator->getStore()->getId()
            );
        }
        return $meta;
    }

    /**
     * @param CookieConsentEavAttribute $attribute
     * @return mixed|null
     */
    public function setupAttributeData(CookieConsentEavAttribute $attribute)
    {
        $model = $this->locator->getModel();
        $modelId = $model->getId();

        if ($modelId) {
            return $this->getValue($attribute);
        }

        return null;
    }

    /**
     * Customize checkboxes
     *
     * @param CookieConsentEavAttribute $attribute
     * @param array                     $meta
     * @return array
     */
    private function customizeCheckbox(CookieConsentEavAttribute $attribute, array $meta): array
    {
        if ($attribute->getFrontendInput() === 'boolean') {
            $meta['arguments']['data']['config']['prefer'] = 'toggle';
            $meta['arguments']['data']['config']['valueMap'] = [
                'true' => '1',
                'false' => '0',
            ];
        }

        return $meta;
    }

    /**
     * Retrieve form element
     *
     * @param string $value
     * @return mixed
     */
    private function getFormElementsMapValue($value)
    {
        $valueMap = $this->formElementMapper->getMappings();

        return $valueMap[$value] ?? $value;
    }

    /**
     * Retrieve attribute value
     *
     * @param CookieConsentEavAttribute $attribute
     * @return mixed
     */
    private function getValue(CookieConsentEavAttribute $attribute)
    {
        $model = $this->locator->getModel();

        return $model->getData($attribute->getAttributeCode());
    }

    /**
     * Retrieve scope label
     *
     * @param CookieConsentEavAttribute $attribute
     * @return \Magento\Framework\Phrase|string
     */
    private function getScopeLabel(CookieConsentEavAttribute $attribute)
    {
        if ($this->storeManager->isSingleStoreMode()
            || $attribute->getFrontendInput() === CookieConsentEavAttribute::FRONTEND_INPUT
        ) {
            return '';
        }

        switch ($attribute->getScope()) {
            case ProductAttributeInterface::SCOPE_GLOBAL_TEXT:
                return __('[GLOBAL]');
            case ProductAttributeInterface::SCOPE_WEBSITE_TEXT:
                return __('[WEBSITE]');
            case ProductAttributeInterface::SCOPE_STORE_TEXT:
                return __('[STORE VIEW]');
        }

        return '';
    }

    /**
     * Whether attribute can have default value
     *
     * @param CookieConsentEavAttribute $attribute
     * @return bool
     */
    private function canDisplayUseDefault(CookieConsentEavAttribute $attribute): bool
    {
        $attributeCode = $attribute->getAttributeCode();
        $model = $this->locator->getModel();
        if ($model->isLockedAttribute($attributeCode)) {
            return false;
        }

        if (isset($this->canDisplayUseDefault[$attributeCode])) {
            return $this->canDisplayUseDefault[$attributeCode];
        }

        return $this->canDisplayUseDefault[$attributeCode] = (
            ($attribute->getScope() !== ProductAttributeInterface::SCOPE_GLOBAL_TEXT)
            && $model
            && $model->getId()
            && $model->getStoreId()
        );
    }

    /**
     * Check if attribute scope is global.
     *
     * @param CookieConsentEavAttribute $attribute
     * @return bool
     */
    private function isScopeGlobal($attribute): bool
    {
        return $attribute->getScope() === ProductAttributeInterface::SCOPE_GLOBAL_TEXT;
    }

    /**
     * Load attribute model by attribute data object.
     *
     * @param CookieConsentEavAttribute $attribute
     * @return EavAttribute
     */
    private function getAttributeModel($attribute): EavAttribute
    {
        $attributeId = $attribute->getAttributeId();

        if (! array_key_exists($attributeId, $this->attributesCache)) {
            $this->attributesCache[$attributeId] = $this->eavAttributeFactory->create()->load($attributeId);
        }

        return $this->attributesCache[$attributeId];
    }
}
