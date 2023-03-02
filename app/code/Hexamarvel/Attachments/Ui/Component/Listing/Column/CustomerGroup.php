<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Attachments
 */
namespace Hexamarvel\Attachments\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Customer\Model\GroupFactory;

class CustomerGroup extends Column
{
    /**
     * @var GroupFactory
     */
    protected $_customerGroup;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param GroupFactory $customerGroup
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        GroupFactory $customerGroup,
        array $components = [],
        array $data = []
    ) {
        $this->_customerGroup = $customerGroup;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @param array $dataSource
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    /**
     * @param array $item
     * @return string $result
     */
    protected function prepareItem(array $item)
    {
        $result = '';
        foreach (explode(',', $item[$this->getData('name')]) as $key => $groupId) {
            $result .= $this->_customerGroup->create()->load($groupId)->getCustomerGroupCode().'<br />';
        }

        return $result;
    }
}
