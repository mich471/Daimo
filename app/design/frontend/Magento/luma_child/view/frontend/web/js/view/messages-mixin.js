define([
    'Magento_Customer/js/customer-data'
], function (customerData) {
    'use strict';
    return function (target) {
        return target.extend({
            initialize: function () {
                this._super();
                var self = this;
                self.messages.subscribe(function(messages) {
                    if (messages.messages) {
                        if (messages.messages.length > 0) {
                            if (window.screen.width < 800) {
                                setTimeout(function() {
                                    customerData.set('messages', {});
                                }, 5000); //disappear the message in 5 seconds
                            }
                        }
                    }
                });
            }
        });
    }
});
