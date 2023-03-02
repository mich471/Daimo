define([
    'jquery',
    'mage/utils/wrapper',
    'Plumrocket_CookieConsent/js/model/restriction',
], function ($, wrapper, restriction) {
    'use strict';

    return function (s) {
        $.cookie = wrapper.wrapSuper($.cookie, function (name, value, options) {
            var self = this;

            // set
            if (arguments.length > 1 && (!/Object/.test(Object.prototype.toString.call(value)) || value == null)) {
                return restriction.setCookieByCallBack(name, function () {
                    return self._super.apply(self, arguments);
                });
            // get
            } else {
                return self._super.apply(self, arguments);
            }
        });

        return s;
    };
});
