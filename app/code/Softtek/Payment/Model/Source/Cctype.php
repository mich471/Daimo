<?php
/**
 * Config Provider Class
 *
 * @package Softtek_Payment
 * @author Paul Soberanes <paul.soberanes@softtek.com>
 * @copyright © Softtek. All rights reserved.
 */
namespace Softtek\Payment\Model\Source;

class Cctype extends \Magento\Payment\Model\Source\Cctype
{
    public function getAllowedType()
    {
        return ['VI','MC','AE','DI','JBC','OT'];
    }
}
