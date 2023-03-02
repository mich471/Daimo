<?php

/**
 * @author    Amasty Team
 * @copyright Copyright (c) Amasty (http://www.amasty.com)
 * @package   Amasty_Customerattr
 */
namespace Amasty\CustomerAttributes\Model\Validation;

class Nickname
{
    protected $_value = 'validate-nickname';

    /**
     * Retrieve custom values
     *
     * @return array
     */
    public function getValues()
    {
        $values = array('value' => $this->_value,
                        'label' => __(
                            'Nickname validation'
                        )
        );
        return $values;
    }

    /**
     * Retrieve JS code
     *
     * @return string
     */
    public function getJS()
    {
        $message = __(
            'Please use letters only (a-z or A-Z) in this field.'
        );

        $js
            = '
           require([
            \'jquery\',
            \'jquery/validate\'
             ], function ($) {
              $.validator.addMethod(\''. $this->_value .'\', function (value, element)
                {
                    return this.optional(element) || /^[\ç\~A-Za-z\_\s]/i.test(value);
                }, \'' . $message . '\');
            });';


        return $js;
    }
}
