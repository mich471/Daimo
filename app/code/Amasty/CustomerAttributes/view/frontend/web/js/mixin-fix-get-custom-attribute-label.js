define([
    'underscore'
], function (_) {
    'use strict';

    return function (viewComponent) {
        return viewComponent.extend({
            /**
             * fix for m2.3.4 (fixed by Magento in m2.4.0).
             * Prevent fatal on checkout page
             */
            getCustomAttributeLabel: function () {
                if (_.isUndefined(this.source.get('customAttributes'))) {
                    this.source.set('customAttributes', {});
                }

                return this._super();
            }
        });
    };
});
