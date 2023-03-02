/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'Plumrocket_DataPrivacy/js/model/fancybox-fix',
    'mage/template',
    'text!Plumrocket_DataPrivacy/template/popup.html',
    'Plumrocket_DataPrivacy/js/model/print-content'
], function ($, modal, $t, fancyBoxFix, template, popupHtml, printContent) {
    'use strict';

    return {
        modalWindow: {},
        self: this,

        /**
         * Create popUp window for provided element.
         *
         * @param {HTMLElement} element
         * @param {Object} consent
         * @param {int} checkboxId
         *
         */
        createModal: function (element, consent, checkboxId) {
            if (consent.cms_page && ! consent.policy) {// fix for compatibility
                consent.policy = consent.cms_page;
            }

            var options = {
                'type': 'popup',
                'modalClass': 'prgdpr-consent-checkbox-modal',
                'responsive': true,
                'innerScroll': true,
                'title': consent.policy.title,
                'buttons': [
                    {
                        text: $t('I agree'),
                        class: 'action iagree primary',

                        /** @inheritdoc */
                        click: function () {
                            $('#'+checkboxId).prop('checked', true);
                            this.closeModal();
                        }
                    }
                ],
                opened: function ($Event) {
                    $('header.modal-header', $Event.currentTarget).append($('.modal-content .prgdpr-consent-content-actions', $Event.currentTarget));
                    fancyBoxFix.hideFancyBox();
                },
                closed: function ($Event) {
                    fancyBoxFix.showFancyBox();
                }
            };

            $.extend(consent, {'id': checkboxId, 'printButtonLabel': $.mage.__('Print')});
            var popupTemplate = $(template(popupHtml, consent));
            popupTemplate
                .find('.prgdpr-consent-content-actions > a')
                .on('click', printContent.printContent.bind(null, consent.policy.title));
            this.modalWindow[this.addVersionToCheckboxId(checkboxId, consent)] = modal(options, popupTemplate.last());
        },

        /** Show popup window */
        showModal: function (id, data) {
            this.modalWindow[this.addVersionToCheckboxId(id, data)].openModal();
        },

        addVersionToCheckboxId: function (checkboxId, data) {
            if (data.cms_page && ! data.policy) {// fix for compatibility
                data.policy = data.cms_page;
            }

            return data.policy.version
                ? checkboxId + '_' + data.policy.version
                : checkboxId;
        },
    };
});
