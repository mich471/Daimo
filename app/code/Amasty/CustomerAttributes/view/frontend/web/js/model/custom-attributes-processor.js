define([
    'underscore'
], function (_) {
    'use strict';

    return {

        /**
         * Adding to attribute data label for attributes with options
         *
         * @param {Array} customAttributes
         * @return {Array}
         */
        addAttributeLabels: function (customAttributes) {
            var attributesConfig = window.checkoutConfig.amastyCustomerAttributeOptionsConfig,
                attributeConfig;

            if (!_.isEmpty(attributesConfig) && !_.isEmpty(customAttributes)) {
                customAttributes = this.filterCustomAttributes(customAttributes);
                _.map(
                    customAttributes,
                    function (attributeData) {
                        if (_.has(attributesConfig, attributeData.attribute_code)) {
                            attributeConfig = attributesConfig[attributeData.attribute_code];
                            attributeData.label = this.getAttributeLabel(attributeData.value, attributeConfig);
                        }

                        return attributeData;
                    }.bind(this)
                );
            }

            return customAttributes;
        },

        /**
         * Filter custom attributes from unnecessary data
         *
         * @param {Array} customAttributes
         * @return {Array}
         */
        filterCustomAttributes: function (customAttributes) {
            return _.filter(
                customAttributes,
                function (attributeData) {
                    return !attributeData.attribute_code.includes('prepared-for-send');
                }
            );
        },

        /**
         *
         * @param {Array|String} attributeValue
         * @param {Array} attributeConfig
         * @return {String}
         */
        getAttributeLabel: function (attributeValue, attributeConfig) {
            var label = '',
                complexLabels = [];

            if (_.isArray(attributeValue)) {
                _.each(attributeValue, function (value) {
                    complexLabels.push(attributeConfig[value]);
                });
                label = complexLabels.join(', ');
            } else {
                label = attributeConfig[attributeValue];
            }

            return label;
        }
    }
});
