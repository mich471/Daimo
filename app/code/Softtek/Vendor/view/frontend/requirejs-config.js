/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            changePersonType: 'Softtek_Vendor/change-person-type',
            inputMask: 'Softtek_Vendor/jquery.mask',
            cnpjUpdater: 'Softtek_Vendor/js/cnpj-updater',
        },
    },
    config: {
        mixins: {
            'mage/validation': {
                'Softtek_Vendor/js/cnpj-validation': true
            },
            'Magento_Ui/js/lib/validation/validator': {
                'Softtek_Vendor/js/cnpj-validator': true
            }
        }
    }
};
