/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (http://www.amasty.com)
 * @package Amasty_CustomerAttributes
 */

define([
    'ko',
    'underscore',
    'mageUtils',
    'Magento_Ui/js/form/element/date',
    'Amasty_CustomerAttributes/js/form/relationAbstract'
], function (ko, _, utils, Date, relationAbstract) {
    'use strict';

    return Date.extend(relationAbstract).extend({

        /**
         * Calls 'initObservable' of parent, initializes 'options' and 'initialOptions'
         *     properties, calls 'setOptions' passing options to it
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super();
            return this;
        }

    });
});
