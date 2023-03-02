/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

define([
    'jquery',
    'mage/storage'
], function ($, storage) {
    'use strict';

    /**
     * @param {Object} config
     */
    return function (config) {
        $.ajax({
            url: config.loadUrl,
            data: {
                global: false,
                componentName: config.componentName
            },
            method: 'GET',
            showLoader: false,
            dataType: 'json',
            success: function (response) {
                if (response.html) {
                    $(config.loadContainer).html(response.html);
                    try {
                        $(config.loadContainer).trigger('contentUpdated').applyBindings();
                    } catch (e) {
                        /**
                         * in new magento versions we get error:
                         * You cannot apply bindings multiple times to the same element.
                         *
                         * Therefore we try to apply binding on first child.
                         */
                        $(config.loadContainer + ' > div').trigger('contentUpdated').applyBindings();
                    }
                }
            }
        });
    }
});
