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
], function ($,) {
    'use strict';

    $.widget('plum.ajaxSubmitForm', {
        options : {
            disableLoaderOnCheckboxesInited:false,
        },

        _create: function () {
            this._super();
            this.initEvents();
        },

        initEvents: function () {
            this.element.on('submit', this.submit.bind(this));

            if (this.options.disableLoaderOnCheckboxesInited) {
                $(document).on("checkboxes_rendered", function(event) {
                    this.element.loader('hide');
                }.bind(this));
            }
        },

        submit: function (event) {
            var self = this;

            if (this.element.validation() && this.element.validation('isValid')) {
                this.element.submit($.ajax({
                    showLoader: true,
                    method: 'post',
                    data: self.element.serializeArray(),
                    url: self.element.attr('action'),
                }));
            }

            return false;
        }
    });

    return $.plum.ajaxSubmitForm;
});
