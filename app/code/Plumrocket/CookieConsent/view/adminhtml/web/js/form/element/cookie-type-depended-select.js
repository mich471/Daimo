/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

define(['uiRegistry', 'Magento_Ui/js/form/element/select'], function (uiRegistry, select) {
    'use strict';
    return select.extend({
        initialize: function () {
            var type = this._super().initialValue;
            this.onComponentLoad('index = domain', function (component) {
                if ('first' === type) {
                    component.hide();
                } else {
                    component.show();
                }
            });
            return this;
        },

        /**
         * On value change handler.
         *
         * @param {String} type
         */
        onUpdate: function (type) {
            var domainField = uiRegistry.get('index = domain');
            if ('first' === type) {
                domainField.hide();
            } else {
                domainField.show();
            }
            return this._super();
        },

        /**
         * Fix for async loading.
         *
         * As components don't have sort order for loading,
         * we need to interact with another component via load callback.
         *
         * @param {String}   query
         * @param {function} callback
         */
        onComponentLoad: function (query, callback) {
            let waitIteration = 0;
            var domainField = uiRegistry.get(query);

            if (domainField) {
                callback(domainField);
                return;
            }

            let interval = setInterval(function () {
                domainField = uiRegistry.get(query);
                if (domainField) {
                    callback(domainField);
                    clearInterval(interval);
                    return;
                }
                if (waitIteration === 4) {
                    clearInterval(interval);
                    return;
                }
                waitIteration++;
            }, 250);
        },
    });
});
