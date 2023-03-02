define(['jquery'], function($) {
    'use strict';

    return function() {
        $.validator.addMethod(
            'alpha-no-numeric',
            function(value, element) {
                var onlyNumbers = value.replace(/\D/g, '');

                return (onlyNumbers == '');
            },
            $.mage.__('Numeric characters are not allowed')
        )
    }
});
