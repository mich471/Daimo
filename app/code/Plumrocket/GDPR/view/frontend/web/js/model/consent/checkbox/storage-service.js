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
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

define([
    'ko',
    'underscore',
    'jquery',
], function (ko, _, $) {
    'use strict';

    var checkboxes = {};
    var checkboxesIdsByLocations = {};
    var listsByLocations = {};

    var sourceUrl = ko.observable('');

    /**
     * @type {{}}
     */
    var storageOptions = {};

    /**
     * Used for waiting all configurations (sourceUrl), and then make a request
     */
    var queue = {
        locationKey: null
    };

    var storageService =  {

        /**
         * Add subscriptions to observables
         *
         * @return {storageService}
         */
        init: function () {
            sourceUrl.subscribe(this.loadListFromQueue.bind(this));
            return this;
        },

        /**
         * @param {{sourceUrl: string}} options
         * @return {storageService}
         */
        setOptions: function (options) {
            if (options.sourceUrl) {
                sourceUrl(options.sourceUrl);
            }
            storageOptions = options;
            return this;
        },

        /**
         * Retrieve observableArray witch will be loaded by filter state
         *
         * @param {String}  locationKey
         * @return {ko.observableArray}
         */
        getList: function (locationKey) {
            this.init();

            this.loadList(locationKey);

            return this.getListByLocation(locationKey);
        },

        /**
         * Retrieve observableArray witch will be loaded
         *
         * @param {String}  locationKey
         * @return {ko.observableArray}
         */
        getListByLocation: function (locationKey) {
            if (! listsByLocations.hasOwnProperty(locationKey)) {
                listsByLocations[locationKey] = ko.observableArray([]);
            }

            return listsByLocations[locationKey];
        },

        /**
         * Perform load checkboxes from server
         *
         * @param locationKey
         */
        loadList: function (locationKey) {
            if (this.isAllConfigDefined()) {
                if (! checkboxes.hasOwnProperty(locationKey)) {
                    $.ajax({
                        url: sourceUrl(),
                        type: 'GET',
                        dataType: 'json',
                        data: {locationKey: locationKey},
                        success: this.saveCheckboxes.bind(this, locationKey),
                        error: $.proxy(this._onError, this),
                        showLoader: false,
                        dontHide: false,
                    });
                }
            } else {
                queue.locationKey = locationKey;
            }
        },

        /**
         * Load list by saved locationKey
         */
        loadListFromQueue: function () {
            if (queue.locationKey && this.isAllConfigDefined()) {
                this.loadList(queue.locationKey);
            }
        },

        /**
         * Check if all configuration exists for ajax request
         *
         * @return {Boolean}
         */
        isAllConfigDefined: function () {
            return Boolean(sourceUrl());
        },

        /**
         * Log error message in console
         *
         * @param error
         * @private
         */
        _onError: function (error) {
            if (error.responseJSON) {
                console.warn(JSON.parse(error.responseJSON));
            } else {
                if (error.responseText) {
                    console.warn(error.responseText);
                }
            }
        },

        /**
         * Find checkbox in cache
         *
         * @param {Number} checkboxId
         * @return {undefined}
         */
        getFromCache: function (checkboxId) {
            return checkboxes[checkboxId];
        },

        /**
         * @param {String} locationKey
         * @param {Array} items
         */
        saveCheckboxes: function (locationKey, items) {
            checkboxesIdsByLocations[locationKey] = [];
            _.each(items, function (item) {
                checkboxesIdsByLocations[locationKey].push(item.consentId);
                checkboxes[item.consentId] = item;
            });

            listsByLocations[locationKey](checkboxesIdsByLocations[locationKey].map(this.getFromCache.bind(this)));
        },
    };

    storageService.init = _.once(storageService.init);

    return storageService.init();
});
