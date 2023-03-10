<?php

namespace Amasty\CustomerAttributes\Block\Data\Form\Element;

class Date extends \Magento\Framework\Data\Form\Element\Date
{
    /**
     * Set correct calendar js config for custom Attributes
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = parent::getElementHtml();

        $html .= '<script type="text/javascript">
                    require.config({"map": {"*": {"calendar": "mage/calendar"}}});
                    </script>';
        return $html;
    }
}
