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

namespace Plumrocket\GDPR\Ui\DataProvider\Checkbox\Form\Modifier;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as EavAttribute;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier as ProductAbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Plumrocket\GDPR\Model\Checkbox\Attribute as CheckboxAttribute;

class Eav implements ModifierInterface
{
    const SORT_ORDER_MULTIPLIER = 10;
    const DATA_SCOPE_CHECKBOX = 'checkbox';
    const DATA_SOURCE_DEFAULT = 'checkbox';

    /**
     * @var \Plumrocket\GDPR\Model\Locator\LocatorInterface
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
     * @var \Plumrocket\GDPR\Model\ResourceModel\Checkbox\Attribute\CollectionFactory
     */
    private $attributeCollectionFactory;

    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\Checkbox
     */
    private $checkboxResource;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * Eav constructor.
     *
     * @param \Magento\Framework\App\ResourceConnection                                 $resource
     * @param \Plumrocket\GDPR\Model\Locator\LocatorInterface                           $locator
     * @param StoreManagerInterface                                                     $storeManager
     * @param \Magento\Ui\DataProvider\Mapper\FormElement                               $formElementMapper
     * @param \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory                 $eavAttributeFactory
     * @param ArrayManager                                                              $arrayManager
     * @param \Magento\Catalog\Model\Attribute\ScopeOverriddenValue                     $scopeOverriddenValue
     * @param \Magento\Framework\App\Request\DataPersistorInterface                     $dataPersistor
     * @param \Plumrocket\GDPR\Model\ResourceModel\Checkbox                             $checkboxResource
     * @param \Plumrocket\GDPR\Model\ResourceModel\Checkbox\Attribute\CollectionFactory $attributeCollectionFactory
     * @param array                                                                     $attributesToDisable
     * @param array                                                                     $attributesToEliminate
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Plumrocket\GDPR\Model\Locator\LocatorInterface $locator,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Ui\DataProvider\Mapper\FormElement $formElementMapper,
        \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $eavAttributeFactory,
        \Magento\Framework\Stdlib\ArrayManager $arrayManager,
        \Magento\Catalog\Model\Attribute\ScopeOverriddenValue $scopeOverriddenValue,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Plumrocket\GDPR\Model\ResourceModel\Checkbox $checkboxResource,
        \Plumrocket\GDPR\Model\ResourceModel\Checkbox\Attribute\CollectionFactory $attributeCollectionFactory,
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
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->checkboxResource = $checkboxResource;
    }

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
     * @param CheckboxAttribute[] $attributes
     * @return array
     */
    private function getAttributesMeta(array $attributes)
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
        if (! $this->locator->getCheckbox()->getId() && $this->dataPersistor->get('prgdpr_checkbox')) {
            return $this->resolvePersistentData($data);
        }

        $checkboxId = $this->locator->getCheckbox()->getId();

        $attributes = $this->getAttributes();

        foreach ($attributes as $attribute) {
            if (null !== ($attributeValue = $this->setupAttributeData($attribute))) {
                $data[$checkboxId][self::DATA_SOURCE_DEFAULT][$attribute->getAttributeCode()] = $attributeValue;
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
    private function resolvePersistentData(array $data)
    {
        $persistentData = (array)$this->dataPersistor->get('prgdpr_checkbox');
        $this->dataPersistor->clear('prgdpr_checkbox');
        $checkboxId = $this->locator->getCheckbox()->getId();

        if (empty($data[$checkboxId][self::DATA_SOURCE_DEFAULT])) {
            $data[$checkboxId][self::DATA_SOURCE_DEFAULT] = [];
        }

        $data[$checkboxId] = array_replace_recursive(
            $data[$checkboxId][self::DATA_SOURCE_DEFAULT],
            $persistentData
        );

        return $data;
    }

    /**
     * Retrieve attributes
     *
     * @return CheckboxAttribute[]
     */
    private function getAttributes()
    {
        if (! $this->attributes) {

            /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection $collection */
            $collection = $this->attributeCollectionFactory->create();
            $eavEntityAttributeTbl = $this->resource->getTableName('eav_entity_attribute');

            $collection->setEntityTypeFilter($this->checkboxResource->getTypeId())
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
     * Check is checkbox already new or we trying to create one
     *
     * @return bool
     */
    private function isCheckboxExists()
    {
        return (bool) $this->locator->getCheckbox()->getId();
    }

    /**
     * @param CheckboxAttribute $attribute
     * @param           $sortOrder
     * @return array
     */
    public function setupAttributeMeta(CheckboxAttribute $attribute, $sortOrder)
    {
        $configPath = ltrim(ProductAbstractModifier::META_CONFIG_PATH, ArrayManager::DEFAULT_PATH_DELIMITER);
        $attributeCode = $attribute->getAttributeCode();
        $meta = $this->arrayManager->set($configPath, [], [
            'dataType' => $attribute->getFrontendInput(),
            'formElement' => $this->getFormElementsMapValue($attribute->getFrontendInput()),
            'visible' => $attribute->getIsVisible(),
            'required' => $attribute->getIsRequired(),
            'notice' => $attribute->getNote() === null ? null : __($attribute->getNote()),
            'default' => (! $this->isCheckboxExists()) ? $attribute->getDefaultValue() : null,
            'label' => __($attribute->getDefaultFrontendLabel()),
            'code' => $attributeCode,
            'source' => 'checkbox',
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

        $checkbox = $this->locator->getCheckbox();
        if (in_array($attributeCode, $this->attributesToDisable, true)
            || $checkbox->isLockedAttribute($attributeCode)) {
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
    private function convertOptionsValueToString(array $options)
    {
        array_walk($options, static function (&$value) {
            if (isset($value['value']) && is_scalar($value['value'])) {
                $value['value'] = (string)$value['value'];
            }
        });

        return $options;
    }

    /**
     * Adds 'use default value' checkbox.
     *
     * @param CheckboxAttribute $attribute
     * @param array             $meta
     * @return array
     */
    private function addUseDefaultValueCheckbox(CheckboxAttribute $attribute, array $meta)
    {
        $canDisplayService = $this->canDisplayUseDefault($attribute);
        if ($canDisplayService) {
            $meta['arguments']['data']['config']['service'] = [
                'template' => 'ui/form/element/helper/service',
            ];

            $meta['arguments']['data']['config']['disabled'] = ! $this->scopeOverriddenValue->containsValue(
                \Plumrocket\GDPR\Api\Data\CheckboxInterface::class,
                $this->locator->getCheckbox(),
                $attribute->getAttributeCode(),
                $this->locator->getStore()->getId()
            );
        }
        return $meta;
    }

    /**
     * @param CheckboxAttribute $attribute
     * @return mixed|null
     */
    public function setupAttributeData(CheckboxAttribute $attribute)
    {
        $checkbox = $this->locator->getCheckbox();
        $checkboxId = $checkbox->getId();

        if ($checkboxId) {
            return $this->getValue($attribute);
        }

        return null;
    }

    /**
     * Customize checkboxes
     *
     * @param CheckboxAttribute $attribute
     * @param array             $meta
     * @return array
     */
    private function customizeCheckbox(CheckboxAttribute $attribute, array $meta)
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
     * @param CheckboxAttribute $attribute
     * @return mixed
     */
    private function getValue(CheckboxAttribute $attribute)
    {
        $checkbox = $this->locator->getCheckbox();

        return $checkbox->getData($attribute->getAttributeCode());
    }

    /**
     * Retrieve scope label
     *
     * @param CheckboxAttribute $attribute
     * @return \Magento\Framework\Phrase|string
     */
    private function getScopeLabel(CheckboxAttribute $attribute)
    {
        if ($this->storeManager->isSingleStoreMode()
            || $attribute->getFrontendInput() === CheckboxAttribute::FRONTEND_INPUT
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
     * @param CheckboxAttribute $attribute
     * @return bool
     */
    private function canDisplayUseDefault(CheckboxAttribute $attribute)
    {
        $attributeCode = $attribute->getAttributeCode();
        $checkbox = $this->locator->getCheckbox();
        if ($checkbox->isLockedAttribute($attributeCode)) {
            return false;
        }

        if (isset($this->canDisplayUseDefault[$attributeCode])) {
            return $this->canDisplayUseDefault[$attributeCode];
        }

        return $this->canDisplayUseDefault[$attributeCode] = (
            ($attribute->getScope() !== ProductAttributeInterface::SCOPE_GLOBAL_TEXT)
            && $checkbox
            && $checkbox->getId()
            && $checkbox->getStoreId()
        );
    }

    /**
     * Check if attribute scope is global.
     *
     * @param CheckboxAttribute $attribute
     * @return bool
     */
    private function isScopeGlobal($attribute)
    {
        return $attribute->getScope() === ProductAttributeInterface::SCOPE_GLOBAL_TEXT;
    }

    /**
     * Load attribute model by attribute data object.
     *
     * @param CheckboxAttribute $attribute
     * @return EavAttribute
     */
    private function getAttributeModel($attribute)
    {
        $attributeId = $attribute->getAttributeId();

        if (!array_key_exists($attributeId, $this->attributesCache)) {
            $this->attributesCache[$attributeId] = $this->eavAttributeFactory->create()->load($attributeId);
        }

        return $this->attributesCache[$attributeId];
    }
}
