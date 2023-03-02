var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-shipping-information': {
                'Amasty_CustomerAttributes/js/action/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/model/new-customer-address': {
                'Amasty_CustomerAttributes/js/model/new-customer-address-mixin': true
            },
            'Magento_Checkout/js/view/shipping-information/address-renderer/default': {
                'Amasty_CustomerAttributes/js/mixin-fix-get-custom-attribute-label': true
            },
            'Magento_Checkout/js/view/billing-address': {
                'Amasty_CustomerAttributes/js/mixin-fix-get-custom-attribute-label': true
            },
            'Magento_Checkout/js/view/shipping-address/address-renderer/default': {
                'Amasty_CustomerAttributes/js/mixin-fix-get-custom-attribute-label': true
            },
            'Temando_Shipping/js/view/checkout/shipping-information/address-renderer/shipping' : {
                'Amasty_CustomerAttributes/js/Temando/shipping-address-renderer-fix': true
            }
        }
    }
};
