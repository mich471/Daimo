<?php

namespace Softtek\CheckoutBR\Model\Config\Source;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;

use Magento\Framework\Serialize\SerializerInterface;
use Softtek\CheckoutBR\Helper\Data as Helper;

/**
 *
 * Add prefix for customer address street
 *
 *
 * NOTICE OF LICENSE
 *
 * @category   Softtek
 * @package    Softtek_CheckoutBR
 * @author     www.sofftek.com
 * @copyright  Softtek Brasil
 * @license    http://opensource.org/licenses/osl-3.0.php
 */
class Streetprefix extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource {

    /**
     * @var OptionFactory
     */
    protected $optionFactory;

    /**
     * Json Serializer
     *
     * @var SerializerInterface
     */
    protected $serializer;

    protected $helper;

    public function __construct(
        Helper $helper,
        SerializerInterface $serializer
    )
    {
        $this->helper = $helper;
        $this->serializer = $serializer;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if($this->helper->getConfig('checkoutbr/general/prefix_enabled')){
            $options = $this->helper->getConfig('checkoutbr/general/prefix_options');
            $optionsArr = $this->serializer->unserialize($options);

            $this->_options[] =  ['label' => __('Please select a street prefix.'), 'value' => ''];
            foreach ($optionsArr as $op){
                $this->_options[] =  ['label' => $op["prefix_options"], 'value' => $op["prefix_options"]];
            }

            return $this->_options;
        }
        return [];
    }
}
