/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

define([
    'ko',
    'jquery',
    'uiComponent',
    'Plumrocket_DataPrivacy/js/model/consent/checkbox/modal',
    'Plumrocket_DataPrivacy/js/model/consent/checkbox/storage-service',
], function (ko, $, Component, consentModal, checkboxStorageService ) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Plumrocket_GDPR/consent-checkbox'
        },

        initialize: function () {
            this._super();

            console.log(this);
            checkboxStorageService.setOptions({sourceUrl: this.sourceUrl});
            this.checkboxes = checkboxStorageService.getList(this.locationKey);
        },

        /**
         * Retrieve current checkbox location
         * @return String
         */
        getLocationKey: function () {
            return this.locationKey;
        },

        /**
         * @deprecated
         * @return String
         */
        getPrgdprLocation: function () {
            return this.getLocationKey();
        },

        /**
         * Show consent content in modal
         *
         * @param {Object} data
         * @param {Object} event
         */
        showContent: function (data, event) {
            if (this.denyToOpenCmsInPopup || this.denyToOpenPolicyInPopup) {
                return true;
            }

            var checkboxId = $(event.target).data('checkboxid');
            consentModal.showModal(checkboxId, data);
        },

        /**
         * Build a unique id for the checkbox
         * Now it uses scope and checkbox id
         *
         * It may not work on checkout
         *
         * @param {Object} context
         * @param {Number} consentId
         */
        getCheckboxId: function (context, consentId) {
            return context.$data.name + '_' + consentId;
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
         * After template rendered
         */
        checkboxesRendered: function () {
            $(document).trigger('checkboxes_rendered');
        },
    });
});
