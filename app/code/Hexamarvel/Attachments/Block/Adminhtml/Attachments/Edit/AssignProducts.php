<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Attachments
 */
namespace Hexamarvel\Attachments\Block\Adminhtml\Attachments\Edit;

class AssignProducts extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'products/assign_products.phtml';

    /**
     * @var \Magento\Catalog\Block\Adminhtml\Category\Tab\Product
     */
    protected $blockGrid;

    /**
     * @var \Hexamarvel\Attachments\Model\ResourceModel\Attachments\CollectionFactory
     */
    protected $_attachmentFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Hexamarvel\Attachments\Model\AttachmentsFactory $attachmentFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Hexamarvel\Attachments\Model\AttachmentsFactory $attachmentFactory,
        array $data = []
    ) {
        $this->_attachmentFactory = $attachmentFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'Hexamarvel\Attachments\Block\Adminhtml\Attachments\Edit\Tab\Product',
                'category.product.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return string
     */
    public function getProductsJson()
    {
        $id = (int)$this->getRequest()->getParam('id', false);
        if (!empty($id)) {
            $products = $this->_attachmentFactory->create()->load($id);

            if (!empty($products->getProducts())) {
                return $products->getProducts();
            }
        }

        return '{}';
    }
}
