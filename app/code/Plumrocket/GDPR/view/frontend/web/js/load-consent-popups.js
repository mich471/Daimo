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
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

/* jscs:disable */
/* eslint-disable */
define([
    'jquery',
    'mage/storage',
    'Magento_Customer/js/customer-data'
], function ($, storage, customerData) {
    'use strict';

    /**
     * @param {Object} config
     */
    return function (config) {
        let popupsAndNotifies = customerData.get('data_privacy');

        if (popupsAndNotifies().countNotifies !== 0 || popupsAndNotifies().countPopups !== 0) {
            loadPopups(config);
        } else {
            popupsAndNotifies.subscribe(
                (dataPrivacyData) => {
                    if (dataPrivacyData.countPopups !== 0
                        || dataPrivacyData.countNotifies !== 0
                    ) {
                        loadPopups(config);
                    }
                }
            );
        }
    }

    function loadPopups(config) {
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
