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
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

define([
    'underscore',
    'ko',
    'jquery',
    'uiComponent',
    'prCookieRestriction',
    'uiRegistry',
    'Magento_Ui/js/modal/modal',
    'text!Plumrocket_CookieConsent/template/notice/modal/modal-bottom.html',
    'text!Plumrocket_CookieConsent/template/notice/modal/modal-popup.html',
    'mage/translate',
    'mage/cookies',
], function (_, ko, $, Component, prCookieRestriction, uiRegistry, modal, modalBottomTpl, modalPopupTpl) {
    'use strict';

    var DISPLAY_STYLE_POPUP = 'popup';
    var DISPLAY_STYLE_WALL = 'wall';
    var DISPLAY_STYLE_BOTTOM = 'bottom';

    return Component.extend({
        /**
         * @type {{
         *     titleColor: string,
         *     textColor: string,
         *     backgroundColor: string,
         *     overlayBackgroundColor: string,
         *     overlayBlur: boolean,
         * }}
         */
        design: {},
        initialize: function (config) {
            this._super();
            this.createModal = _.once(this.createModal);
        },

        autoOpen: function () {
            this.createModal();
            if (! prCookieRestriction.isOptIn() && ! this.isDisabled()) {
                this.getModal().openModal();
            }

            this.addStyles(this.design);

            this.addChangingStyleOnHover($('.pr-cookie-notice-btn.allow'), this.acceptButtonConfig);
            this.addChangingStyleOnHover($('.pr-cookie-notice-btn.decline'), this.declineButtonConfig);
            this.addChangingStyleOnHover($('.pr-cookie-setting-btn'), this.settingsButtonConfig);
        },

        allowAll: function () {
            prCookieRestriction.allowAllCategories();
            this.getModal().closeModal();
        },

        declineAll: function () {
            prCookieRestriction.declineAll();
            this.getModal().closeModal();
        },

        openSettings: function () {
            uiRegistry.get('pr-cookie-settings-bar').getModal().openModal();
        },

        /**
         * @return {String}
         */
        getDisplayStyle: function () {
            return this.displayStyle;
        },

        createModal: function () {
            var self = this;
            var options = {
                buttons: [],
                modalClass: 'pr-cookie-modal pr-cookie-modal-' + this.getDisplayStyle(),
            };

            switch (this.getDisplayStyle()) {
                case DISPLAY_STYLE_BOTTOM:
                    options = _.extendOwn(options, {
                        customTpl: modalBottomTpl,
                        type: 'custom',
                        overlayClass: 'pr-cookie-overlay',
                        parentModalClass: '',
                        modalVisibleClass: 'show-without-overlay',
                        keyEventHandlers: {
                            escapeKey: function () {}
                        }
                    });
                    break;

                case DISPLAY_STYLE_WALL:
                    options = _.extendOwn(options, {
                        popupTpl: modalPopupTpl,
                        type: 'popup',
                        clickableOverlay: false,
                        canClose: false,
                        title: this.noticeTitle,
                        keyEventHandlers: {
                            escapeKey: function () {}
                        },
                        opened: function () {
                            self.design.overlayBlur && self.overlayBlur.enable();
                        },
                        closed: function () {
                            self.design.overlayBlur && self.overlayBlur.disable();
                        }
                    });
                    break;

                case DISPLAY_STYLE_POPUP:
                    options = _.extendOwn(options, {
                        popupTpl: modalPopupTpl,
                        type: 'popup',
                        clickableOverlay: false,
                        canClose: true,
                        title: this.noticeTitle,
                        modalCloseBtnHandler: this.disableNotice.bind(this),
                        keyEventHandlers: {
                            escapeKey: this.disableNotice.bind(this)
                        },
                        opened: function () {
                            self.design.overlayBlur && self.overlayBlur.enable();
                        },
                        closed: function () {
                            self.design.overlayBlur && self.overlayBlur.disable();
                        },
                    });
                    break;
            }

            this.modal = modal(options, document.getElementById('pr-cookie-notice'));
        },

        getModal: function () {
            this.createModal();
            return this.modal;
        },

        /**
         * Check if notice was closed by customer this day
         *
         * @return {boolean}
         */
        isDisabled: function () {
            return 'close' === $.mage.cookies.get(this.statusCookieName);
        },

        /**
         * Temporally disable notice
         * Don't execute on "closed" event because customer might close popup by accepting cookie
         */
        disableNotice: function () {
            $.mage.cookies.set(this.statusCookieName, 'close', {lifetime: 86400}); // one day
            this.getModal().closeModal();
        },

        /**
         * @param $button
         * @param {{type, text_color, text_color_on_hover, background_color, background_color_on_hover}} config
         */
        addChangingStyleOnHover: function ($button, config) {
            $button.hover(
                function () {
                    $(this).css({
                        'background-color': (config.type === 'link') ? '' : config.background_color_on_hover,
                        'border-color': (config.type === 'link') ? '' : config.background_color_on_hover,
                        'color': config.text_color_on_hover
                    })
                },
                function () {
                    $(this).css({
                        'background-color': (config.type === 'link') ? '' : config.background_color,
                        'border-color': (config.type === 'link') ? '' : config.background_color,
                        'color': config.text_color
                    });
                }
            );
        },

        /**
         *
         * @param {{titleColor, textColor, backgroundColor, overlayBackgroundColor}} design
         */
        addStyles: function (design) {
            if (design.titleColor) {
                $('.pr-cookie-modal .modal-title').css({'color': design.titleColor});
            }

            if (design.textColor) {
                $('.pr-cookie-notice-text').css({'color': design.textColor});
            }

            var background = document.querySelector('.pr-cookie-modal > .modal-inner-wrap');
            if (design.backgroundColor && background) {
                background.style.backgroundColor = design.backgroundColor;
            }

            var overlayBackground = document.querySelector('.pr-cookie-modal + .modals-overlay');
            if (design.overlayBackgroundColor && overlayBackground) {
                overlayBackground.style.backgroundColor = design.overlayBackgroundColor;
            }
        },

        overlayBlur: {
            enable: function () {
                document.querySelector('.page-wrapper').classList.add('blur-mode');
            },
            disable: function () {
                document.querySelector('.page-wrapper').classList.remove('blur-mode');
            }
        }
    });
});
