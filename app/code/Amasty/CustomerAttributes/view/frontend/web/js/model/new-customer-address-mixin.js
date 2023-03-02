define([
    'mage/utils/wrapper',
    'Amasty_CustomerAttributes/js/model/custom-attributes-processor'
], function (wrapper, attributeProcessor) {
    'use strict';

    return function (newAddress) {
        return wrapper.wrap(newAddress, function (newAddressAction, addressData) {

            if (addressData.custom_attributes) {
                addressData.custom_attributes = attributeProcessor.addAttributeLabels(addressData.custom_attributes);
            }

            return newAddressAction(addressData);
        });
    };
});
