<?php

namespace Softtek\WidgetProducts\Block\BestSeller;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Softtek\WidgetProducts\Helper\Data;

class Index extends Template implements BlockInterface
{
    /**
     * Default faq template
     * @var string
     */
    protected $_template = 'Softtek_WidgetProducts::best_seller.phtml';

    public $helperData;

    public function __construct(
        Template\Context $context,
        Data $helperData
    )
    {
        $this->helperData = $helperData;
        parent::__construct($context);
    }

    public function getProductDetailsHtml(\Magento\Catalog\Model\Product $product)
    {
        $renderer = $this->getDetailsRenderer($product->getTypeId());
        if ($renderer) {
            $renderer->setProduct($product);
            return $renderer->toHtml();
        }
        return '';
    }

    public function getDetailsRenderer($type = null)
    {
        if ($type === null) {
            $type = 'default';
        }
        $rendererList = $this->getDetailsRendererList();
        if ($rendererList) {
            return $rendererList->getRenderer($type, 'default');
        }
        return null;
    }

    protected function getDetailsRendererList()
    {
        return $this->getChildBlock(
            'details.renderers'
        );
    }
}
