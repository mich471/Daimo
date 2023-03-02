<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Attachments
 */
namespace Hexamarvel\Attachments\Block\Adminhtml\Renderer\Attachments\Filter;

class CustomerGroup extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Text
{
    /**
     * @var \Magento\Customer\Model\GroupFactory
     */
    protected $customerGroup;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\DB\Helper $resourceHelper,
        \Magento\Customer\Model\GroupFactory $customerGroup,
        array $data = []
    ) {
        $this->_resourceHelper = $resourceHelper;
        $this->_customerGroup = $customerGroup;
        parent::__construct($context, $resourceHelper, $data);
    }

    /**
     * @return array
     */
    protected function _getOptions()
    {
        $customerGroup = $this->_customerGroup->create()->getCollection();
        $options[] = ['value' => '', 'label' => ''];
        foreach ($customerGroup as $key => $group) {
            $options[] = ['value' => $group->getId(), 'label' => $group->getCustomerGroupCode()];
        }

        return $options;
    }

    /**
     * Render an option with selected value
     *
     * @param array $option
     * @param string $value
     * @return string
     */
    protected function _renderOption($option, $value)
    {
        $selected = $option['value'] == $value && $value !== null ? ' selected="selected"' : '';
        return '<option value="' . $this->escapeHtml(
            $option['value']
        ) . '"' . $selected . '>' . $this->escapeHtml(
            $option['label']
        ) . '</option>';
    }

    /**
     * {@inheritdoc}
     */
    public function getHtml()
    {
        $html = '<select name="' . $this->_getHtmlName() . '" id="' . $this->_getHtmlId() . '"' . $this->getUiId(
            'filter',
            $this->_getHtmlName()
        ) . 'class="no-changes admin__control-select">';
        $value = $this->getValue();
        foreach ($this->_getOptions() as $option) {
            if (is_array($option['value'])) {
                $html .= '<optgroup label="' . $this->escapeHtml($option['label']) . '">';
                foreach ($option['value'] as $subOption) {
                    $html .= $this->_renderOption($subOption, $value);
                }
                $html .= '</optgroup>';
            } else {
                $html .= $this->_renderOption($option, $value);
            }
        }

        $html .= '</select>';
        return $html;
    }
}
