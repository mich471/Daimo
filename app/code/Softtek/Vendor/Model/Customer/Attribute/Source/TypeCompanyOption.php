<?php

namespace Softtek\Vendor\Model\Customer\Attribute\Source;

class TypeCompanyOption extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * @inheritdoc
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        if (!$this->_options) {
            $this->_options = [
                ['value' => '', 'label' => __('Selecione tipo de empresa')],
                ['value' => '1', 'label' => __('Convertedor/Transformador')],
                ['value' => '2', 'label' => __('Co-Packer/Envase')],
                ['value' => '3', 'label' => __('Dono de marca')],
                ['value' => '4', 'label' => __('Distribuidor')],
                ['value' => '5', 'label' => __('Outros')]
            ];
        }
        return $this->_options;
    }
}
