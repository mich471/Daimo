<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Attachments
 */
namespace Hexamarvel\Attachments\Block\Adminhtml\Renderer\Attachments;

use Magento\Framework\DataObject;

class Document extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @param  DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        return $this->prepareItem($value);
    }

    /**
     * @param string $name
     * @return string $result
     */
    protected function prepareItem($name)
    {
        $fileUrl = $this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ) . 'hexaattachment/products/attachments/' . $name;

        return '<a href="'.$fileUrl.'" target="_blank">' . $name . '</a>';
    }
}
