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
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

define([
    'ko',
    'jquery',
    'uiComponent',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function (ko, $, Component, alert) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Plumrocket_GDPR/account-check'
        },

        initialize: function (config) {
            this._super();
        },

        emailPlaceholder: $.mage.__('guest@email.com'),
        buttonValue: $.mage.__('Access My Data'),
        toggleLoader: ko.observable(false),

        sendEmailForm: function (model, event) {
            var form = $(event.target).closest('form'),
                posting,
                self = this;

            if (form.validation() && form.validation('isValid')) {
                posting = $.post(form.attr("action"), form.serialize());
                self.toggleLoader(true);
                posting.done(function (data) {
                    self.toggleLoader(false);

                    if (data.success === true) {
                        self.showMessage(
                            $.mage.__('Thank you!'),
                            $.mage.__('Please check your email (%1) for further instructions.').replace('%1', data.email),
                            "prgdrp-alert-success"
                        );
                    } else {
                        var errorMesssage =  $.mage.__('We couldn\'t find any user data associated with this email (%1). You can try again with different email address.').replace('%1', data.email);

                        if (data.message) {
                            errorMesssage = data.message;
                        }

                        self.showMessage(
                            $.mage.__('Sorry!'),
                            errorMesssage,
                            "prgdrp-alert-error"
                        );
                    }
                });
            }
        },

        showMessage: function (title, message, alertClass) {
            alert({
                title: title,
                content: "<div class='prgdpr-checking-message'><span class='prgdpr-checking-message-icon "
                    + alertClass + "'></span><span class='prgdpr-checking-message-text'>"
                    + message + "</span></div>"
            });
        }
    });
});