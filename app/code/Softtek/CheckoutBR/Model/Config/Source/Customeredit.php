<?php
namespace Softtek\CheckoutBR\Model\Config\Source;

/**
 *
 * Add options to select about customer can edit after account created
 *
 * NOTICE OF LICENSE
 *
 * @category   Softtek
 * @package    Softtek_CheckoutBR
 * @author     www.sofftek.com
 * @copyright  Softtek Brasil
 * @license    http://opensource.org/licenses/osl-3.0.php
 */
class Customeredit implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('No')],
            ['value' => 'yes', 'label' => __('Yes, except change person type')],
            ['value' => 'yesall', 'label' => __('Yes, and allow change person type')]
        ];
    }
}
