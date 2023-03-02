<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (http://www.amasty.com)
 * @package Amasty_CustomerAttributes
 */

namespace Amasty\CustomerAttributes\Plugin\Customer\Ui;

use Magento\Customer\Api\Data\AttributeMetadataInterface as AttributeMetadata;

class ColumnFactory
{
    /**
     * set magento data model for checkxoxes and radios
     * @param \Magento\Customer\Ui\Component\ColumnFactory $subject
     * @param $result
     * @return mixed
     */
    public function beforeCreate(
        \Magento\Customer\Ui\Component\ColumnFactory $subject,
        $attributeData,
        $columnName,
        $context,
        $config = []
    ) {
        switch ($attributeData['frontend_input']) {
            case 'selectimg':
            case 'selectgroup':
                $config['dataType'] = 'select';
                $config['component'] = 'Magento_Ui/js/grid/columns/select';
                $attributeData['frontend_input'] = 'select';
                break;
            case 'multiselectimg':
                $config['dataType'] = 'select';
                $config['component'] = 'Magento_Ui/js/grid/columns/select';
                $attributeData['frontend_input'] = 'multiselect';
                break;
        }

        return [$attributeData, $columnName, $context, $config];
    }

    /**
     * TODO::DELETE THIS PLUGIN AFTER FIX \Magento\Customer\Ui\Component\ColumnFactory LINE 85
     * Broken logic app/code/Magento/Ui/view/base/web/js/grid/columns/date.js LINE 53
     *
     * @param \Magento\Customer\Ui\Component\ColumnFactory $subject
     * @param \Magento\Ui\Component\Listing\Columns\ColumnInterface $result
     * @param array $attributeData
     * @param string $columnName
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param array $config
     * @return \Magento\Ui\Component\Listing\Columns\ColumnInterface
     */
    public function afterCreate(
        \Magento\Customer\Ui\Component\ColumnFactory $subject,
        $result,
        array $attributeData,
        $columnName,
        $context,
        array $config = []
    ) {
        $config = $result->getData();
        if ($attributeData[AttributeMetadata::FRONTEND_INPUT] == 'date' && $config['config']['timezone'] === false) {
            $config['config']['timezone'] = 'false';
            $result->setData($config);

            return $result;
        }

        return $result;
    }
}
