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
    'text!Plumrocket_CookieConsent/template/settings/modal/modal-slide.html',
    'mage/translate'
], function (_, ko, $, Component, prCookieRestriction, uiRegistry, modal, modalSlideTpl) {
    'use strict';

    return Component.extend({
        acceptButtonLabel: '',

        /**
         * Contains selected categories to confirm
         */
        selectedCategories: [],

        initialize: function (config) {
            this._super();
            this.createModal = _.once(this.createModal);
            this.isDetailView = ko.observable(false);
            this.currentCategory = ko.observable('');

            _.each(this.categories, function (category) {
                if (prCookieRestriction.isAllowedCategory(category.key) || this.isPreChecked(category.key)) {
                    this.selectedCategories.push(category.key);
                }
            }.bind(this));

            this.initOpenCookieSettingsLinks('pr-open-cookie-settings')
        },

        isAllowed: function (categoryKey) {
            return prCookieRestriction.isAllowedCategory(categoryKey) || this.isPreChecked(categoryKey);
        },

        isPreChecked: function (categoryKey) {
            return ! prCookieRestriction.isOptIn() && this.getCategoryByKey(categoryKey).is_pre_checked;
        },

        /**
         * Save Choices
         */
        confirmChosen: function () {
            prCookieRestriction.setCustomerConsent(this.selectedCategories);
            this.closeNotice();
            this.getModal().closeModal();
        },

        /**
         * Toggle is allowed category in local variable, to save chooses - use "this.confirmChosen"
         *
         * @param uiClass
         * @param {jQuery.Event} jQueryEvent
         */
        toggleSelectedCategory: function (uiClass, jQueryEvent) {
            /** @type {HTMLInputElement} input */
            var input = jQueryEvent.target;
            var categoryKey = input.name;
            if (input.checked) {
                this.selectedCategories.push(categoryKey);
            } else {
                this.selectedCategories = _.without(this.selectedCategories, categoryKey);
            }
        },

        createModal: function () {
            var options = {
                slideTpl: modalSlideTpl,
                type: 'slide',
                buttons: [],
                modalClass: 'pr-cookie-left-bar',
            };

            this.modal = modal(options, document.getElementById('pr-cookie-setting-bar'));

            this.addChangingStyleOnHover($('.pr-cookie-left-bar .action.confirm'), this.confirmButtonConfig);
            this.addChangingStyleOnHover($('.pr-cookie-left-bar .action.allow'), this.acceptButtonConfig);
            this.addChangingStyleOnHover($('.pr-cookie-left-bar .action.decline'), this.declineButtonConfig);
        },

        getModal: function () {
            this.createModal();
            return this.modal;
        },

        closeNotice: function () {
            uiRegistry.get('pr-cookie-notice').getModal().closeModal();
        },

        /**
         * @param $button
         * @param {{type, text_color, text_color_on_hover, background_color, background_color_on_hover}} config
         */
        addChangingStyleOnHover: function ($button, config) {
            $button.hover(
                function () {
                    $(this).css({
                        'background-color': config.background_color_on_hover,
                        'border-color': config.background_color_on_hover,
                        'color': config.text_color_on_hover
                    })
                },
                function () {
                    $(this).css({
                        'background-color': config.background_color,
                        'border-color': config.background_color,
                        'color': config.text_color
                    });
                }
            );
        },

        allowAll: function () {
            prCookieRestriction.allowAllCategories();
            this.closeNotice();
            this.getModal().closeModal();
        },

        declineAll: function () {
            prCookieRestriction.declineAll();
            this.closeNotice();
            this.getModal().closeModal();
        },

        showCookieDetails: function (uiClass, jQueryEvent) {
            this.currentCategory(jQueryEvent.target.dataset.categoryKey);
            this.isDetailView(true);
        },

        hideCookieDetails: function () {
            this.isDetailView(false);
        },

        /**
         * Get category by key
         *
         * @param {string} categoryKey
         * @return {{}}
         */
        getCategoryByKey: function (categoryKey) {
            return _.find(this.categories, function (category) {
                return category.key === categoryKey;
            });
        },

        /**
         * Get category name by key
         *
         * @param {string} categoryKey
         * @return {string}
         */
        getCategoryNameByKey: function (categoryKey) {
            var category = this.getCategoryByKey(categoryKey);
            return category ? category.name : categoryKey;
        },

        /**
         * @param {string} categoryKey
         * @return {[]}
         */
        getGroupedCookies: function (categoryKey) {
            var firstPartyCookies = [];
            var thirdPartyCookies = [];

            var result = [];

            this.cookies.forEach(function (cookie) {
                if (cookie.category_key === categoryKey) {
                    if (cookie.type === 'first') {
                        firstPartyCookies.push(cookie);
                    } else {
                        thirdPartyCookies.push(cookie);
                    }
                }
            });

            if (firstPartyCookies.length > 0) {
                result.push(
                    {
                        name: $.mage.__('First party'),
                        cookies: firstPartyCookies
                    }
                );
            }

            var groupdThirdPartyCookies = thirdPartyCookies.reduce(function (cookiesByDomain, cookie) {
                if (!cookiesByDomain[cookie.domain]) {
                    cookiesByDomain[cookie.domain] = [];
                }
                cookiesByDomain[cookie.domain].push(cookie);
                return cookiesByDomain;
            }, {});

            _.each(groupdThirdPartyCookies, function (cookiesWithSameDomain) {
                result.push({
                    name: cookiesWithSameDomain[0].domainLabel,
                    cookies: cookiesWithSameDomain
                });
            });

            return result;
        },

        /**
         * @param {string} categoryKey
         * @return {boolean}
         */
        isCategoryHasCookie: function (categoryKey) {
            return _.some(this.cookies, function (cookie) {
                return cookie.category_key === categoryKey;
            });
        },

        closeModal: function () {
            this.getModal().closeModal();
        },

        /**
         * Add event listener to show this component (Settings Panel)
         *
         * We have added this logic to component to avoid separate js file loading
         *
         * @param openSettingsClass
         */
        initOpenCookieSettingsLinks(openSettingsClass) {
            $('body').on('click', function (event) {
                if (event.target.classList.contains(openSettingsClass)) {
                    this.getModal().openModal();
                }
            }.bind(this));
        }
    });
});
