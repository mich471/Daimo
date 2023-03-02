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
            var options = {
                'type': 'popup',
                'modalClass': 'prgdpr-consent-checkbox-modal',
                'responsive': true,
                'innerScroll': true,
                'title': consent.cms_page.title,
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
                .on('click', printContent.printContent.bind(null, consent.cms_page.title));
            this.modalWindow[this.addVersionToCheckboxId(checkboxId, consent)] = modal(options, popupTemplate.last());
        },

        /** Show popup window */
        showModal: function (id, data) {
            this.modalWindow[this.addVersionToCheckboxId(id, data)].openModal();
        },

        addVersionToCheckboxId: function (checkboxId, data) {
            return data.cms_page.version
                ? checkboxId + '_' + data.cms_page.version
                : checkboxId;
        },
    };
});
