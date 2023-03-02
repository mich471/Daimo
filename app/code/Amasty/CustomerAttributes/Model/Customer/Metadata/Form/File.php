<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (http://www.amasty.com)
 * @package Amasty_CustomerAttributes
 */
namespace Amasty\CustomerAttributes\Model\Customer\Metadata\Form;

use Magento\Framework\App\Area;
use Magento\Framework\File\UploaderFactory;
use Magento\Framework\Filesystem;

class File extends \Magento\Customer\Model\Metadata\Form\File
{
    /**
     * @var \Magento\Customer\Model\AttributeFactory
     */
    private $attributeFactory;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;
    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    private $dataObjectFactory;
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Api\Data\AttributeMetadataInterface $attribute,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Customer\Model\AttributeFactory $attributeFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        $value,
        $entityTypeCode,
        $isAjax,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\MediaStorage\Model\File\Validator\NotProtectedExtension $fileValidator,
        Filesystem $fileSystem,
        UploaderFactory $uploaderFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\State $state
    ) {
        $this->attributeFactory = $attributeFactory;
        $this->messageManager = $messageManager;
        $this->dataObjectFactory = $dataObjectFactory;

        parent::__construct(
            $localeDate,
            $logger,
            $attribute,
            $localeResolver,
            $value,
            $entityTypeCode,
            $isAjax,
            $urlEncoder,
            $fileValidator,
            $fileSystem,
            $uploaderFactory
        );
        $this->registry = $registry;
        $this->state = $state;
    }

    /**
     * rewrite validate function for adding validation rules
     * @param array|null|string $value
     * @return array|bool
     */
    public function validateValue($value)
    {
        $attribute = $this->getAttribute();

        /** @var $model \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
        $model = $this->attributeFactory->create();
        $model->loadByCode($this->_entityTypeCode, $attribute->getAttributeCode());
        $validationRules = [];
        if ($model->getFileTypes()) {
            $validationRules[] = $this->dataObjectFactory->create([
                'data' => [
                    'name'  => 'file_extensions',
                    'value' => $model->getFileTypes()
                ]
            ]);
        }
        if ((int)$model->getFileSize() > 0) {
            $validationRules[] = $this->dataObjectFactory->create([
                'data' => [
                    'name'  => 'max_file_size',
                    'value' => ((int)$model->getFileSize() * 1024 * 1024)
                ]
            ]);
        }
        $attribute->setValidationRules($validationRules);

        return parent::validateValue($value);
    }

    public function extractValue(\Magento\Framework\App\RequestInterface $request)
    {
        $value = parent::extractValue($request);
        if (!$value) {
            $extend = $this->_getRequestValue($request);
            if (isset($extend['value'])) {
                /* TODO generate link with html*/
                $value = $extend['value'];
            }
        }
        return $value;
    }

    /**
     * @param array|string $value
     * @return array|bool|\Magento\Framework\Api\Data\ImageContentInterface|null|string
     */
    public function compactValue($value)
    {
        if ($this->state->getAreaCode() === Area::AREA_ADMINHTML) {
            return parent::compactValue($value);
        }

        $result = $this->validateValue($value);
        if ($result === true) {
            return parent::compactValue($value);
        }

        $this->registry->register('amasty_customerAttributes_validation-failed', true);
        foreach ($result as $error) {
            $this->messageManager->addErrorMessage($error);
        }

        return '';
    }

    public function outputValue($format = \Magento\Customer\Model\Metadata\ElementFactory::OUTPUT_FORMAT_TEXT)
    {
        $value = parent::outputValue($format);
        if (!$value && $format == 'html') {
            $value = $this->_value;
        }
        return $value;
    }
}
