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
    'Plumrocket_GDPR/js/model/consent/checkbox/storage-service',
], function (ko, $, Component, consentModal, checkboxStorageService ) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Plumrocket_GDPR/consent-checkbox'
        },

        initialize: function () {
            this._super();

            checkboxStorageService.setOptions({sourceUrl: this.sourceUrl});
            this.checkboxes = checkboxStorageService.getList(this.locationKey);
        },

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
            if (this.denyToOpenCmsInPopup) {
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
