<?php

namespace Softtek\Vendor\Model\Customer\Attribute\Source;

class OperatingSegmentType extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * @inheritdoc
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        if (!$this->_options) {
            $this->_options = [
                ['value' => '', 'label' => __('Por favor, selecione uma segmento de atuação')],
                ['value' => '1', 'label' => __('Agricultura')],
                ['value' => '2', 'label' => __('Automotivo & Transporte')],
                ['value' => '3', 'label' => __('Bens de consumo')],
                ['value' => '4', 'label' => __('Comida e Bebida')],
                ['value' => '5', 'label' => __('Construções')],
                ['value' => '6', 'label' => __('Cuidados pessoais')],
                ['value' => '7', 'label' => __('Industrial')],
                ['value' => '8', 'label' => __('Tintas e revestimentos')],
                ['value' => '9', 'label' => __('Outros')]
            ];
        }
        return $this->_options;
    }
}
