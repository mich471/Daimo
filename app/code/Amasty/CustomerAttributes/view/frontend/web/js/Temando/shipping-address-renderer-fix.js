define([
    'Magento_Checkout/js/view/shipping-information/address-renderer/default'
], function (defaultRenderer) {
    'use strict';

    return function (viewComponent) {
        return defaultRenderer.extend(viewComponent);
    };
});
