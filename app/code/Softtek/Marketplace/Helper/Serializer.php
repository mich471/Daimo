<?php
/**
 * Softtek_Marketplace SellerData
 *
 * @category    Softtek
 * @package     Softtek_Marketplace
 * @author      J. Abraham Serena <jorge.serena@softtek.com>
 * @copyright   © Softtek 2022. All rights reserved.
 */
namespace Softtek\Marketplace\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Serialize\Serializer\Json;

class Serializer extends AbstractHelper implements ArgumentInterface
{
    /**
     * @var Json
     */
    protected $json;

    /**
     * Serializer Constructor
     *
     * @param Json $json
     */
    public function __construct(
        Json $json
    ) {
        $this->json    =   $json;
    }

    /**
     * Serialize array
     *
     * @param array $value
     */
    public function serialize($value)
    {
        return $this->json->serialize($value);
    }

    /**
     * Unserialize string
     *
     * @param string $value
     */
    public function unserialize($value)
    {
        return $this->json->unserialize($value);
    }
}
