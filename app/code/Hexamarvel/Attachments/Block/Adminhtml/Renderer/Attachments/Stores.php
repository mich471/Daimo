<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Attachments
 */
namespace Hexamarvel\Attachments\Block\Adminhtml\Renderer\Attachments;

use Magento\Framework\DataObject;

class Stores extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Framework\Escaper $escaper
     */
    public function __construct(
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Framework\Escaper $escaper
    ) {
        $this->systemStore = $systemStore;
        $this->escaper = $escaper;
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
     * @param string $origStores
     * @return string $content
     */
    protected function prepareItem($origStores)
    {
        $content = '';
        if (!is_array($origStores)) {
            $origStores= explode(',', $origStores);
        }

        if (in_array(0, $origStores) && count($origStores) == 1) {
            return __('All Store Views');
        }

        $data = $this->systemStore->getStoresStructure(false, $origStores);
        foreach ($data as $website) {
            $content .= $website['label'] . "<br/>";
            foreach ($website['children'] as $group) {
                $content .= str_repeat('&nbsp;', 3) . $this->escaper->escapeHtml($group['label']) . "<br/>";
                foreach ($group['children'] as $store) {
                    $content .= str_repeat('&nbsp;', 6) . $this->escaper->escapeHtml($store['label']) . "<br/>";
                }
            }
        }

        return $content;
    }
}
