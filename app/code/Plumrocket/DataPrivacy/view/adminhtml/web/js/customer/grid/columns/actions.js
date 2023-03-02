/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

define([
    'Magento_Customer/js/grid/columns/actions'
], function (Actions) {
    'use strict';

    return Actions.extend({
        /**
         * Reload listing data source after cancel or delete actions.
         *
         * @param {Object} data
         */
        onAction: function (data) {
            if (data.action === 'delete' || 'cancel' === data.action) {
                this.source().reload({
                    refresh: true
                });
            }
        }
    });
});
