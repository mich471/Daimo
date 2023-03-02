define([
    'jquery',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'domReady'
], function ($, Component, customerData) {
    'use strict';

    return Component.extend({
        /** @inheritdoc */
        initialize: function () {
            var _this = this;

            this._super();

            this.wishlist = customerData.get('wishlist');
            this.wishlist.subscribe(function(newValue) {
                _this.decorateItems();
            });

            _this.decorateItems();
        },

        decorateItems: function() {
            var items = this.wishlist().items;

            if (typeof items === 'undefined' || !items.length) return;

            $('a.action.towishlist').each(function(){
                var data = $(this).data('post'),
                    i;

                for (i = 0; i < items.length; i++) {
                    if (data.data.product == items[i].product_id) {
                        $(this).addClass('wishlisted');
                    }
                }
            });
        }
    });
});
