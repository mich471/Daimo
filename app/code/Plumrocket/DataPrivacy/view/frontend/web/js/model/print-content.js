/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
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
