<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model\Consent\Location\Config;

use Magento\Framework\Config\ConverterInterface;
use Magento\Framework\Data\Argument\Interpreter\Boolean as BooleanInterpreter;
use Magento\Framework\Data\Argument\Interpreter\Number as NumberInterpreter;

/**
 * @since 3.1.0
 */
class Converter implements ConverterInterface
{
    /**
     * @var BooleanInterpreter
     */
    private $booleanInterpreter;

    /**
     * @var NumberInterpreter
     */
    private $numberInterpreter;

    /**
     * @param \Magento\Framework\Data\Argument\Interpreter\Boolean $booleanInterpreter
     * @param \Magento\Framework\Data\Argument\Interpreter\Number  $numberInterpreter
     */
    public function __construct(BooleanInterpreter $booleanInterpreter, NumberInterpreter $numberInterpreter)
    {
        $this->booleanInterpreter = $booleanInterpreter;
        $this->numberInterpreter = $numberInterpreter;
    }

    /**
     * Convert dom node tree to array
     *
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $output = [];
        /** @var \DOMNodeList $locations */
        $locations = $source->getElementsByTagName('location');
        /** @var \DOMNode $location */
        foreach ($locations as $location) {
            $locationConfig = [
                'key' => '',
                'name' => '',
                'type' => 2,
                'description' => '',
                'visible' => true,
            ];
            /** @var \DOMAttr $attribute */
            foreach ($location->attributes as $attribute) {
                $value = $attribute->nodeValue;
                if ('visible' === $attribute->nodeName) {
                    $value = $this->booleanInterpreter->evaluate(['value' => $value]);
                }
                if ('type' === $attribute->nodeName) {
                    $value = (int) $this->numberInterpreter->evaluate(['value' => $value]);
                }
                $locationConfig[$attribute->nodeName] = $value;
            }

            $output[$location->attributes->getNamedItem('key')->nodeValue] = $locationConfig;
        }

        return $output;
    }
}
