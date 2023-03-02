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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

define([
    'mage/utils/wrapper'
], function (wrapper) {
    'use strict';

    var mixin = {
        prGdprSave: function () {
            var saveCallback = function (params) {
                this.save.apply(this, params);
            };

            window.confirmSaveCmsPage(null, saveCallback.bind(this, arguments));
        },
    };

    return function (target) {
        return target.extend(mixin);
    };
});
