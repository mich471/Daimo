define([
    'jquery',
    'mage/utils/wrapper',
    'Plumrocket_CookieConsent/js/model/restriction',
], function ($, wrapper, restriction) {
    'use strict';

    return function (originReturnData) {
        $.mage.cookies.set = wrapper.wrapSuper($.mage.cookies.set, function (name, value, options) {
            restriction.setCookieByCallBack(name, this._super.bind(this, name, value, options));
        });

        return originReturnData;
    };
});
