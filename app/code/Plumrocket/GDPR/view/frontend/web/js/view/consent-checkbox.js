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
    'jquery',
    'uiComponent',
    'Plumrocket_GDPR/js/model/consent-checkbox-modal',
], function (ko, $, Component, consentModal) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Plumrocket_GDPR/consent-checkbox'
        },

        initialize: function () {
            this._super();

            if (typeof this.checkboxes === 'string') {
                this.checkboxes = JSON.parse(this.checkboxes);
            }
        },

        isVisible: true,
        locationKey: window.consentCheckboxesLocation,
        checkboxes: window.consentCheckboxesConfig,

        /**
         * Retrieve current checkbox location
         * @return String
         */
        getPrgdprLocation: function () {
            return this.locationKey;
        },

        /**
         * Show consent content in modal
         *
         * @param {Object} data
         * @param {Object} event
         */
        showContent: function (data, event) {
            var checkboxId = $(event.target).data('checkboxid');
            consentModal.showModal(checkboxId, data);
        },

        /**
         * build a unique id for the term checkbox
         *
         * @param {Object} context
         * @param {Number} consentId
         */
        getCheckboxId: function (context, consentId) {
            var checkboxIdSuffix = '',
                paymentMethodRenderer = context.$parents[1];

            // corresponding payment method fetched from parent context
            if (paymentMethodRenderer) {
                // item looks like this: {title: "Check / Money order", method: "checkmo"}
                checkboxIdSuffix = paymentMethodRenderer.item ?
                  paymentMethodRenderer.item.method : '';
            }

            if (! checkboxIdSuffix) {
                checkboxIdSuffix = context.$data.name;
            }

            return  consentId + '_' + checkboxIdSuffix;
        },

        /**
         * Init modal window for rendered element
         *
         * @param {HTMLElement} element
         * @param {Object} consent
         */
        initModal: function (element, consent) {
            var checkboxId = $(element).data('checkboxid');
            consentModal.createModal(element, consent, checkboxId);
        },

        /**
         * @param {{isAlreadyChecked: boolean}} checkbox
         * @return {boolean}
         */
        isChecked: function (checkbox) {
            return checkbox.isAlreadyChecked === true;
        },

        /**
         * @param {{isAlreadyChecked: boolean, canDecline: boolean}} checkbox
         * @return {boolean}
         */
        isDisabled: function (checkbox) {
            return checkbox.isAlreadyChecked && checkbox.canDecline === false;
        },

        /**
         * After template rendered
         */
        checkboxesRendered: function () {
            $(document).trigger('checkboxes_rendered');
        },

        /**
         * @param {{cms_page: {version: string}, policy: {title: string}}} checkbox
         * @return {string|number}
         */
        getCmsPageVersion: function (checkbox) {
            return checkbox.cms_page ? checkbox.cms_page.version : 0;
        },

        getRequiredConsents: function () {
            return Array.isArray(this.checkboxes)
                ? this.checkboxes.filter(function (checkbox) {
                    return !checkbox.canDecline;
                })
                : [];
        },

        getOptionalConsents: function () {
            return Array.isArray(this.checkboxes)
                ? this.checkboxes.filter(function (checkbox) {
                    return checkbox.canDecline;
                })
                : [];
        },
    });
});
