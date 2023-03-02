<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Attachments
 */
namespace Hexamarvel\Attachments\Block\Adminhtml\Attachments\Edit\Tab;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class Attachments extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Hexamarvel\Attachments\Model\ResourceModel\Attachments\CollectionFactory
     */
    protected $_attachmentFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Hexamarvel\Attachments\Model\AttachmentsFactory $attachmentFactory
     * @param array $data = []
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Hexamarvel\Attachments\Model\AttachmentsFactory $attachmentFactory,
        array $data = []
    ) {
        $this->_productFactory = $productFactory;
        $this->_attachmentFactory = $attachmentFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('catalog_category_products');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    /**
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_attachment') {
            $attachIds = $this->_getSelectedProducts();
            if (empty($attachIds)) {
                $attachIds = 0;
            }

            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('id', ['in' => $attachIds]);
            } elseif (!empty($attachIds)) {
                $this->getCollection()->addFieldToFilter('id', ['nin' => $attachIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
        if ($this->getRequest()->getParam('id', false)) {
            $this->setDefaultFilter(['in_attachment' => 1]);
        }

        $collection = $this->_attachmentFactory->create()->getCollection()->addFieldToSelect(
            '*'
        );
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        if ($storeId > 0) {
            $collection->addStoreFilter($storeId);
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_attachment',
            [
                'type' => 'checkbox',
                'name' => 'in_attachment',
                'values' => $this->_getSelectedProducts(),
                'index' => 'id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            ]
        );
        $this->addColumn(
            'id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn('name', ['header' => __('Name'), 'index' => 'name']);
        $this->addColumn(
            'file',
            [
                'header' => __('Document'),
                'index' => 'file',
                'renderer' => 'Hexamarvel\Attachments\Block\Adminhtml\Renderer\Attachments\Document'
            ]
        );
        $this->addColumn(
            'customer_group',
            [
                'header' => __('Customer Group'),
                'index' => 'customer_group',
                'filter' => 'Hexamarvel\Attachments\Block\Adminhtml\Renderer\Attachments\Filter\CustomerGroup',
                'renderer' => 'Hexamarvel\Attachments\Block\Adminhtml\Renderer\Attachments\CustomerGroup'
            ]
        );
        $this->addColumn(
            'stores',
            [
                'header' => __('Store View'),
                'index' => 'stores',
                'filter' => false,
                'renderer' => 'Hexamarvel\Attachments\Block\Adminhtml\Renderer\Attachments\Stores'
            ]
        );
        $this->addColumn(
            'is_active',
            [
                'header' => __('Status'),
                'index' => 'is_active',
                'filter' => 'Hexamarvel\Attachments\Block\Adminhtml\Renderer\Attachments\Filter\Status',
                'renderer' => 'Hexamarvel\Attachments\Block\Adminhtml\Renderer\Attachments\Status'
            ]
        );
        $this->addColumn(
            'position',
            [
                'header' => __('Position'),
                'type' => 'number',
                'index' => 'position',
                'editable' => true,
                'column_css_class'=>'no-display',
                'header_css_class'=>'no-display'
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('hexaattachment/products/AttachmentGrid', ['_current' => true]);
    }

    /**
     * @return array/json
     */
    protected function _getSelectedProducts()
    {
        $id = (int)$this->getRequest()->getParam('id', false);
        $attach = $this->getRequest()->getPost('selected_products');
        if (!empty($id)) {
            $attachments = $this->_attachmentFactory->create()->getCollection()->addFieldtoFilter(
                'products',
                [
                    'like' => '%"'.$id .'"%'
                 ]
            );

            foreach ($attachments as $key => $attachment) {
                $attach[] = $attachment->getId();
            }
        }

        return $attach;
    }
}
