<?php
declare(strict_types=1);

namespace Amasty\CustomerAttributes\Plugin\CustomerCustomAttributes\Helper\Data;

use Magento\CustomerCustomAttributes\Helper\Data;

class PrepareAttributeInputTypes
{
    /**
     * @var string[]
     */
    private $inputTypesMap = [
        'multiselectimg' => 'multiselect',
        'selectimg' => 'select'
    ];

    /**
     * Change input type value to prevent errors on native customer attributes form
     *
     * @param Data $subject
     * @param string|null $inputType
     * @return array
     */
    public function beforeGetAttributeInputTypes(Data $subject, $inputType = null): array
    {
        $nativeAttributeInputType = $this->inputTypesMap[$inputType] ?? null;

        if ($nativeAttributeInputType) {
            $inputType = $nativeAttributeInputType;
        }

        return [$inputType];
    }
}
