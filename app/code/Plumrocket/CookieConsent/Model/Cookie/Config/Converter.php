<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Model\Cookie\Config;

use Magento\Framework\Config\ConverterInterface;
use Magento\Framework\Data\Argument\Interpreter\Constant;
use Plumrocket\CookieConsent\Model\Cookie\Attribute\Source\Type;

class Converter implements ConverterInterface
{
    /**
     * @var \Magento\Framework\Data\Argument\Interpreter\Constant
     */
    private $constInterpreter;

    public function __construct(Constant $constInterpreter)
    {
        $this->constInterpreter = $constInterpreter;
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
        /** @var \DOMNodeList $cookies */
        $cookies = $source->getElementsByTagName('cookie');
        /** @var \DOMNode $cookie */
        foreach ($cookies as $cookie) {
            $cookieConfig = [];
            foreach ($cookie->attributes as $attribute) {
                $cookieConfig[$attribute->nodeName] = $attribute->nodeValue;
            }
            /** @var \DOMNode $childNode */
            foreach ($cookie->childNodes as $childNode) {
                if ($childNode->nodeType == XML_ELEMENT_NODE ||
                    ($childNode->nodeType == XML_CDATA_SECTION_NODE ||
                    $childNode->nodeType == XML_TEXT_NODE && trim(
                        $childNode->nodeValue
                    ) != '')
                ) {
                    if ($childNode->attributes->getNamedItem('type')
                        && 'const' === $childNode->attributes->getNamedItem('type')->nodeValue
                    ) {
                        $value = $this->constInterpreter->evaluate(['value' => $childNode->nodeValue]);
                    } else {
                        $value = $childNode->nodeValue;
                    }

                    $cookieConfig[$childNode->nodeName] = $value;
                }
            }
            $output[$cookie->attributes->getNamedItem('name')->nodeValue] = $cookieConfig;
        }

        $output = array_map(static function ($cookie) {
            $cookie['type'] = $cookie['type'] ?? Type::TYPE_FIRST;
            $cookie['category_key'] = $cookie['category'];
            unset($cookie['category']);
            return $cookie;
        }, $output);

        return ['cookies' => $output];
    }
}
