

define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'Magento_Ui/js/modal/confirm',
    'Magento_Checkout/js/view/minicart',
    'mage/translate'
], function ($, customerData, confirm, minicart) {
    'use strict';

    var addToCartMixin = {
        /**
         * Handler for the form 'submit' event
         *
         * @param {jQuery} form
         */
        submitForm: function (form) {
            var original = this._super.bind(this);
            const self = this;
            if (this.needSellerConfirm(form)) {
                confirm({
                    title: $.mage.__('Você só pode adicionar produtos de 1 vendedor ao carrinho'),
                    content: $.mage.__('Deseja remover os produtos do carrinho e adicionar este produto?'),
                    buttons: [{
                        text: $.mage.__('Cancel'),
                        class: 'action-secondary action-dismiss',

                        /**
                         * Click handler.
                         */
                        click: function (event) {
                            this.closeModal(event);
                            return;
                        }
                    }, {
                        text: $.mage.__('Confirm'),
                        class: 'action-primary action-accept',

                        /**
                         * Click handler.
                         */
                        click: function () {
                            self.removeOtherSellerItems(form);
                            this.closeModal(event);
                            return original(form);
                        }
                    }]
                });
            } else {
                return original(form);
            }
        },

        removeOtherSellerItems: function (form) {
            const cartItems = this.getCartItems();
            const action = form.attr('action');
            const sellerId = this.getSellerIdFromUrl(action);
            const otherSellerItems = cartItems.filter(function (item) {
                return item.seller_id != sellerId;
            });

            if (otherSellerItems.length > 0) {
                otherSellerItems.forEach(function (item) {
                    const miniCart = $('[data-block=\'minicart\']');
                    const sidebar = miniCart.data('mageSidebar');
                    if (sidebar) {
                        sidebar._removeItem($('<div>').data('cart-item', item.item_id));
                    }
                });
            }
        },

        needSellerConfirm: function (form) {
            let needed = false;
            const action = form.attr('action');
            const cartItems = this.getCartItems();

            const sellerId = this.getSellerIdFromUrl(action);
            if (cartItems) {
                const otherSellerItems = cartItems.filter(function (item) {
                    return item.seller_id != sellerId;
                });

                if (otherSellerItems.length > 0) {
                    needed = true;
                }
            }

            return needed;
        },

        getCartItems: function () {
            const getCartData = customerData.get('cart');
            const cartData = getCartData();

            return cartData.items;
        },

        getSellerIdFromUrl: function (url) {
            var sellerId = null;
            var urlParts = url.split('/');
            urlParts = urlParts.filter(Boolean);

            $.each(urlParts, function (key, part) {
                part = part.replace(':', '');
                if (part === 'seller_id') {
                    if (urlParts[key + 1]) {
                        sellerId = urlParts[key + 1];
                    }
                }
            });

            return sellerId;
        }
    };

    return function (targetWidget) {
        $.widget('mage.catalogAddToCart', targetWidget, addToCartMixin);

        return $.mage.catalogAddToCart;
    };
});
