/**
 * @copyright © Softtek. All rights reserved.
 * @author Paul Soberanes <paul.soberanes@softtek.com>
 */
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push({
            type: 'softtek_payment',
            component: 'Softtek_Payment/js/view/payment/method-renderer/cc_method'
        });

        /**
         * Add view logic here
         * Add Fingerprint.js here
         */
        return Component.extend({});
    }
);
