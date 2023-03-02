/**
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

define([
    'underscore',
    'Plumrocket_CookieConsent/js/lib/js.cookie',
    'jquery'
], function (_, jsCookie, $) {
    'use strict';

    var CUSTOMER_CONSENT = 'pr-cookie-consent';

    var SYSTEM_COOKIES = [
        'PHPSESSID',
        CUSTOMER_CONSENT,
        'user_allowed_save_cookie',
    ];

    /**
     * Contain state of model initializing.
     *
     * @type {boolean}
     */
    var isConfigured = false;

    /**
     * Configuration of cookie restrictions
     */
    var config = {
        canManageCookie: null,
        canUseCookieBeforeOptIn: null,
        canBlockUnknownCookie: null,
        consent: {
            isLoggedIn: null,
            logUrl: null,
            reloadAfterAccept: null,
            reloadAfterDecline: null,
            expiry: null
        },
        cookie: {
            path: null,
            domain: null
        },
        mage: {
            website: null,
            cookieName: null,
            lifetime: null
        },
        cookieToCategoryMapping: null,
        essentialCategoryKeys: null,
        dynamicNamesPatterns: []
    };

    /**
     * List of callbacks and categories they depend on.
     *
     * @type {[{callback: function, categoryKey: string}]}
     */
    var userScripts = [];

    function CookieRestriction()
    {
        /**
         * Allow show logs of work
         *
         * 0 - disabled
         * 1 - only warnings
         * 2 - all logs
         *
         * @type {number}
         */
        this.debugMode = 0;

        /**
         * Contains callbacks that run after all configuration come
         *
         * @type {function[]}
         */
        this.configuredCallbacks = [];

        /**
         * If js try to set cookie (except systems cookies) before configs are parsed we move them into the queue
         * After parsing configs we set all cookie from queue using
         * @see this.configuredCallbacks
         *
         * @type {{}}
         */
        this.queue = {};

        /**
         * All cookies must be set by this function
         *
         * @param cookieName
         * @param cookieSetter
         */
        this.setCookieByCallBack = function (cookieName, cookieSetter) {
            this.log('Catch set cookie "' + cookieName + '"');

            if (this.isSystemCookie(cookieName)) {
                this.log('System cookie "' + cookieName + '" have set');
                return cookieSetter();
            }

            if (this.isConfigured()) {
                if (this.isAllowed(cookieName)) {
                    this.log('Allowed cookie "' + cookieName + '" has set');
                    return cookieSetter()
                } else {
                    this.warn('Setting of cookie "' + cookieName + '" was blocked by cookie consent extension');
                }
            } else {
                this.addToQueue(cookieName, cookieSetter);
            }
        };

        /**
         * @return {CookieRestriction}
         */
        this.allowAllCategories = function () {
            this.setCustomerConsent(['all']);
            return this.cache.reset();
        };

        /**
         * @return {CookieRestriction}
         */
        this.declineAll = function () {
            this.setCustomerConsent([]);
            return this.cache.reset();
        };

        /**
         * Retrieve if configs are set
         *
         * @return {boolean}
         */
        this.isConfigured = function () {
            return isConfigured;
        };

        /**
         * @param {function} callback
         * @return {CookieRestriction}
         */
        this.addConfiguredCallback = function (callback) {
            this.configuredCallbacks.push(callback);
            return this;
        };

        /**
         * Retrieve if guest/customer has cookie consent or has allowed cookie before.
         *
         * @return {boolean}
         */
        this.isOptIn = function () {
            var allowedWebsites = this.websiteRestriction.getAllowed();
            var allowedWithDefaultCookie = allowedWebsites[config.mage.website] === 1;

            return Boolean(this.getCustomerConsent()) || allowedWithDefaultCookie;
        };

        /**
         * Retrieve if guest/customer has cookie consent
         *
         * @return {array|null}
         */
        this.getCustomerConsent = function () {
            var consent = jsCookie.get(CUSTOMER_CONSENT);

            /**
             * todo: remove in 2022
             *
             * Previously we used "*" for "all categories" but cookie with this value is blocked by mod security
             * @see REQUEST-942-APPLICATION-ATTACK-SQLI
             */
            if (consent) {
                consent = consent.replace('*', 'all')
            }

            return consent ? JSON.parse(consent) : false;
        };

        /**
         * Save customer consent
         */
        this.setCustomerConsent = function (allowedCategories) {
            jsCookie.set(CUSTOMER_CONSENT, JSON.stringify(allowedCategories), {
                expires: config.consent.expiry,
                path: config.cookie.path,
                domain: config.cookie.domain
            });

            if (_.contains(allowedCategories, 'all')) {
                this.websiteRestriction.allowCurrent();
            } else {
                this.websiteRestriction.disallowCurrent();
            }

            var isAccepting = allowedCategories.length;
            var self = this;

            $.post(config.consent.logUrl, {acceptedKeys: allowedCategories})
                .always(function () {
                    self.reloadAfterAction(isAccepting);
                });
        };

        /**
         * This logic has php alternative in \Plumrocket\CookieConsent\Model\Cookie\IsAllowed
         *
         * @param {String} cookieName
         * @return {boolean}
         */
        this.isAllowed = function (cookieName) {
            if (this.isSystemCookie(cookieName)) {
                return true;
            }

            if (! config.canManageCookie) {
                return true;
            }

            cookieName = this.getTrueCookieName(cookieName);

            if (! this.isOptIn()) {
                if (config.canUseCookieBeforeOptIn) {
                    return true;
                }

                if (this.isKnownCookie(cookieName)) {
                    return this.isInEssentialCategory(cookieName);
                }

                return false;
            }

            if (! this.isKnownCookie(cookieName)) {
                return ! config.canBlockUnknownCookie;
            }

            return this.isInAllowedCategory(cookieName);
        };

        /**
         * This logic has php alternative in \Plumrocket\CookieConsent\Model\Category\IsAllowed
         *
         * @param {string} categoryKey
         * @return {boolean}
         */
        this.isAllowedCategory = function (categoryKey) {
            if (! config.canManageCookie) {
                return true;
            }

            if (this.isEssentialCategory(categoryKey)) {
                return true;
            }

            if (this.isOptIn()) {
                if (this.isAllCategoriesAllowed()) {
                    return true;
                }

                return _.contains(this.getCustomerConsent(), categoryKey);
            }

            return config.canUseCookieBeforeOptIn;
        };

        this.clearRejectedCookie = function () {
            var self = this;

            _.each(jsCookie.get(), function (value, cookieName) {
                if (! self.isAllowed(cookieName)) {
                    jsCookie.remove(cookieName);
                    self.warn('Remove cookie "' + cookieName + '"');
                }
            });
        };

        /**
         * If cookie is system, it set immediately without any validations
         *
         * @param {string} cookieName
         * @return {boolean}
         */
        this.isSystemCookie = function (cookieName) {
            return _.contains(SYSTEM_COOKIES, cookieName);
        };

        /**
         * @param {string} cookieName
         * @return {boolean}
         */
        this.isKnownCookie = function (cookieName) {
            return config.cookieToCategoryMapping.hasOwnProperty(cookieName);
        };

        /**
         * @param {string} cookieName
         * @return {string}
         */
        this.getTrueCookieName = function (cookieName) {
            var dynamicName = _.findKey(config.dynamicNamesPatterns, function (dynamicNamePattern) {
                return new RegExp(dynamicNamePattern).test(cookieName)
            });
            return dynamicName ? dynamicName : cookieName;
        };

        /**
         * @return {boolean}
         */
        this.isAllCategoriesAllowed = function () {
            if (null === this.cache.get('allowAllCategories')) {
                var customerConsent = this.getCustomerConsent();
                if (customerConsent && _.contains(customerConsent, 'all') ) {
                    this.cache.set('allowAllCategories', true);
                } else {
                    var allowedWebsites = this.websiteRestriction.getAllowed();
                    this.cache.set('allowAllCategories', allowedWebsites[config.mage.website] === 1);
                }
            }

            return this.cache.get('allowAllCategories');
        };

        /**
         * @param {string} cookieName
         * @return {string|undefined}
         */
        this.getCookieCategory = function (cookieName) {
            return config.cookieToCategoryMapping[cookieName];
        };

        /**
         * @param {string} cookieName
         * @return {boolean}
         */
        this.isInEssentialCategory = function (cookieName) {
            return this.isEssentialCategory(this.getCookieCategory(cookieName));
        };

        /**
         * @param {string} categoryKey
         * @return {boolean}
         */
        this.isEssentialCategory = function (categoryKey) {
            return _.contains(config.essentialCategoryKeys, categoryKey);
        };

        /**
         * @return {boolean}
         */
        this.isInAllowedCategory = function (cookieName) {
            return this.isAllowedCategory(this.getCookieCategory(cookieName));
        };

        /**
         * @param {string} configJson
         */
        this.configure = function (configJson) {
            try {
                config = JSON.parse(configJson);
                if (config.canManageCookie === null) {
                    this.log('Cookie restriction config is invalid', this);
                }
                isConfigured = true;
            } catch (e) {
                this.log('Error has happened during parse JSON', this);
                return false;
            }

            this.configuredCallbacks.forEach(function (callback) {
                callback();
            }.bind(this));

            this.log('Cookie restriction configuration complete', config);

            this.clearRejectedCookie();
        };

        /**
         * Reload page by configuration and action
         *
         * @param isAccepting
         */
        this.reloadAfterAction = function (isAccepting) {
            if (isAccepting && config.consent.reloadAfterAccept) {
                window.location.reload();
            }

            if (! isAccepting && config.consent.reloadAfterDecline) {
                window.location.reload();
            }
        };

        /**
         * @param {string}   cookieName
         * @param {function} cookieSetter
         */
        this.addToQueue = function (cookieName, cookieSetter) {
            this.queue[cookieName] = cookieSetter;
            this.log('Cookie "' + cookieName + '" was add to queue');
        };

        this.runQueue = function () {
            _.each(this.queue, function (cookieSetter, cookieName) {
                this.log('Set Cookie "' + cookieName + '" by queue');
                this.setCookieByCallBack(cookieName, cookieSetter);
            }.bind(this));
        };

        this.addConfiguredCallback(this.runQueue.bind(this));

        /**
         * Log actions if enabled debug mode
         */
        this.log = function () {
            if (this.debugMode > 1) {
                console.log.apply(null, arguments);
            }
        };

        /**
         * Log actions if enabled debug mode
         */
        this.warn = function () {
            if (this.debugMode > 0) {
                console.warn.apply(null, arguments);
            }
        };

        /**
         * Run script after model is configured.
         *
         * @param callback
         */
        this.execute = function (callback) {
            if (this.isConfigured()) {
                callback();
            } else {
                this.addConfiguredCallback(callback);
            }
        };
    }

    /**
     * Keep information for optimization
     */
    CookieRestriction.prototype.cache = {
        originCacheData: undefined,
        cacheData: {
            allowAllCategories: null
        },

        get: function (key) {
            return this.cacheData[key];
        },

        set: function (key, value) {
            if (! this.originCacheData) {
                this.originCacheData = this.cacheData;
            }
            this.cacheData[key] = value;
            return this;
        },

        reset: function () {
            if (this.originCacheData) {
                this.cacheData = this.originCacheData;
            }
            return this;
        }
    };

    /**
     * Object for work with magento restrictions by websites
     */
    CookieRestriction.prototype.websiteRestriction = {
        getAllowed: function () {
            var allowedWebsites = jsCookie.getJSON(config.mage.cookieName);
            return (allowedWebsites !== undefined) ? allowedWebsites : {};
        },

        allowCurrent: function () {
            return this.set(config.mage.website, true);
        },

        disallowCurrent: function () {
            return this.set(config.mage.website, false);
        },

        /**
         * @param {number} website
         * @param {boolean} flag
         * @return {CookieRestriction.websiteRestriction}
         */
        set: function (website, flag) {
            var allowedWebsites = this.getAllowed();

            if (flag) {
                allowedWebsites[website] = 1;
            } else {
                delete allowedWebsites[website];
            }

            this.jsCookieWithConverter.set(config.mage.cookieName, allowedWebsites, {
                path: config.cookie.path,
                expires: config.mage.lifetime ? Math.ceil(config.mage.lifetime / 86400) : 0
            });
            return this;
        },

        /**
         * 'jsCookie' with custom value converter for write.
         * Save cookie in same format as magento do.
         */
        jsCookieWithConverter: jsCookie.withConverter({
            write: function (value, key) {
                return encodeURIComponent(value);
            }
        })
    };

    /**
     * Object for integrating user scripts.
     */
    CookieRestriction.prototype.userScript = {
        /**
         * @type {CookieRestriction}
         */
        model: null,

        /**
         * Run script after model is configured.
         *
         * @param {CookieRestriction} model
         */
        init: function (model) {
            this.model = model;
            model.addConfiguredCallback(this.executePendingScripts.bind(this));
        },

        /**
         * Run script after model is configured.
         *
         * @param {function} callback
         * @param {string}   categoryKey
         */
        execute: function (callback, categoryKey) {
            if (isConfigured) {
                if (this.model.isAllowedCategory(categoryKey)) {
                    callback();
                }
            } else {
                userScripts.push({callback: callback, categoryKey: categoryKey});
            }
        },

        /**
         * Run pending scripts.
         */
        executePendingScripts: function () {
            userScripts.forEach(function (script) {
                if (this.model.isAllowedCategory(script.categoryKey)) {
                    script.callback();
                }
            }.bind(this));
        },
    };

    var CookieRestrictionModel = new CookieRestriction()
    CookieRestrictionModel.userScript.init(CookieRestrictionModel);
    return CookieRestrictionModel;
});
