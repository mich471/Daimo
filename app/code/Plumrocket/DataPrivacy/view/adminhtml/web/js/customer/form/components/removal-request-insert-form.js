/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

define([
    'Magento_Ui/js/form/components/insert-form'
], function (Insert) {
    'use strict';

    return Insert.extend({
        defaults: {
            listens: {
                responseData: 'onResponse'
            },
            modules: {
                removalRequestListing: '${ $.removalRequestListingProvider }',
                removalRequestModal: '${ $.removalRequestModalProvider }'
            }
        },

        /**
         * Close modal and reload removal request listing
         *
         * @param {Object} responseData
         */
        onResponse: function (responseData) {
            if (! responseData.error) {
                this.removalRequestModal().closeModal();
                this.removalRequestListing().reload();
            }
        }
    });
});
