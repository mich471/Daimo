<?php

namespace Softtek\Attributes\Model\Config\Source;

class OptionsHorario implements \Magento\Framework\Option\ArrayInterface {

    public function toOptionArray(){
        $options = [
            0 => [
                'label' => "--Seleccione una Hora--",
                'value' => ""
            ],
            1 => [
                'label' => "01:00",
                'value' => "010000"
            ],
            2 => [
                'label' => "02:00",
                'value' => "020000"
            ],
            3 => [
                'label' => "03:00",
                'value' => "030000"
            ],
            4 => [
                'label' => "04:00",
                'value' => "040000"
            ],
            5 => [
                'label' => "05:00",
                'value' => "050000"
            ],
            6 => [
                'label' => "06:00",
                'value' => "060000"
            ],
            7 => [
                'label' => "07:00",
                'value' => "070000"
            ],
            8 => [
                'label' => "08:00",
                'value' => "080000"
            ],
            9 => [
                'label' => "09:00",
                'value' => "090000"
            ],
            10 => [
                'label' => "10:00",
                'value' => "100000"
            ],
            11 => [
                'label' => "11:00",
                'value' => "110000"
            ],
            12 => [
                'label' => "12:00",
                'value' => "120000"
            ],
            13 => [
                'label' => "13:00",
                'value' => "130000"
            ],
            14 => [
                'label' => "14:00",
                'value' => "140000"
            ],
            15 => [
                'label' => "15:00",
                'value' => "150000"
            ],
            16 => [
                'label' => "16:00",
                'value' => "160000"
            ],
            17 => [
                'label' => "17:00",
                'value' => "170000"
            ],
            18 => [
                'label' => "18:00",
                'value' => "180000"
            ],
            19 => [
                'label' => "19:00",
                'value' => "190000"
            ],
            20 => [
                'label' => "20:00",
                'value' => "200000"
            ],
            21 => [
                'label' => "21:00",
                'value' => "210000"
            ],
            22 => [
                'label' => "22:00",
                'value' => "220000"
            ],
            23 => [
                'label' => "23:00",
                'value' => "230000"
            ],
            24 => [
                'label' => "00:00",
                'value' => "000000"
            ]
        ];

        return $options;
    }

}