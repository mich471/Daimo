/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

 var config = {
    map: {
        '*': {
            changePersonType: 'Softtek_CheckoutBR/change-person-type',
            inputMask: 'Softtek_CheckoutBR/jquery.mask',
            cnpjUpdater: 'Softtek_CheckoutBR/js/cnpj-updater',
        },
    },
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-shipping-information': {
                'Softtek_CheckoutBR/js/action/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/action/create-shipping-address': {
                'Softtek_CheckoutBR/js/action/create-shipping-address-mixin': true
            },
            'Magento_Checkout/js/action/create-billing-address': {
                'Softtek_CheckoutBR/js/action/create-billing-address-mixin': true
            },
            'Magento_Checkout/js/action/place-order': {
                'Softtek_CheckoutBR/js/action/place-order-mixin': true
            },
            'mage/validation': {
                'Softtek_CheckoutBR/js/cnpj-validation': true
            },
            'Magento_Ui/js/lib/validation/validator': {
                'Softtek_CheckoutBR/js/cnpj-validator': true
            }
        }
    }
};
