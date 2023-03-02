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
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

define([
    'jquery',
], function ($) {
    'use strict';

    return {
        printContent: function (title, event) {
            var contentId = $(event.target).parents('a').data('contentid');
            var content = document.getElementById(contentId).innerHTML;
            var printWindow = window.open('', 'Print', 'height=600,width=800');

            printWindow.document.write('<html><head><title>' + title + '</title>');
            printWindow.document.write('</head><body >');
            printWindow.document.write(content);
            printWindow.document.write('</body></html>');

            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
            return true;
        },
    };
});
