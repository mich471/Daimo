/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/mage'
], function ($) {
    'use strict';

    return function (data) {
        $('.header.links').css(
            "display", "none"
        );
        $($.mage.redirect(data.url, 'assign', 5000));
    };
});
