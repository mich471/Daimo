<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Attachments
 */
namespace Hexamarvel\Attachments\Ui\Component\Listing\Column\Status;

use Magento\Framework\Data\OptionSourceInterface;

class Options implements OptionSourceInterface
{
    /**
     * Disabled value
     */
    const DISABLED = '0';

    /**
     * Enabled value
     */
    const ENABLED = '1';

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {

        $this->currentOptions = [
            'Default' => [
                'label' => __(' '),
                'value' => '-1',
            ],
            'Disabled' => [
                'label' => __('Disabled'),
                'value' => self::DISABLED,
            ],
            'Enabled' => [
                'label' => __('Enabled'),
                'value' => self::ENABLED,
            ],
        ];

        $this->options = array_values($this->currentOptions);
        return $this->options;
    }
}
