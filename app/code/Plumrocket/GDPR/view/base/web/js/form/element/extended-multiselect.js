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
    'underscore',
    'jquery',
    'Magento_Ui/js/form/element/multiselect',
    'mage/translate',
    'Plumrocket_GDPR/js/chosen.jquery.min',
], function (_, $, Multiselect) {
    'use strict';

    return Multiselect.extend({
        defaults: {
            elementTmpl: 'Plumrocket_GDPR/form/element/extended-multiselect',
        },

        /**
         * @var {HTMLElement}
         */
        currentSelect: null,

        getSelect: function () {
            if (! this.currentSelect) {
                this.currentSelect = document.getElementById(this.uid);
            }

            return this.currentSelect;
        },

        afterRender: function () {
            this.setDisabled();
            this.initChosen();
        },

        /**
         * Set attribute "disabled"
         */
        setDisabled: function () {
            var self = this;

            var select = this.getSelect();

            _.each(self.options(), function (group) {
                _.each(group.value, function (item) {
                    if (item.params) {
                        var option = select.querySelector('option[value="' + item.value + '"]');
                        if (option) {
                            _.each(item.params, function (value, attr) {
                                option.setAttribute(attr, value);
                            })
                        }
                    }
                });
            });
        },

        /**
         * Init "chosen" library
         */
        initChosen: function () {
            var select = this.getSelect();
            var $select = $(select);

            var enableSearch = "readonly" !== select.getAttribute('readonly');

            $select.chosen({
                "display_selected_options" : true,
                "display_disabled_options": true,
                "hide_results_on_select": true,
                "group_search": enableSearch
            });

            this.initializeOptionDepends($select);
            this.initUseDefaultListener($select);
        },

        /**
         * Init option dependency
         *
         * @param $select
         */
        initializeOptionDepends: function ($select) {
            var allOptionId = 'all';

            var initialValues = ! $select.val() ? [] : $select.val();
            var initialShowToAllIndex = initialValues.indexOf(allOptionId);

            var self = this;

            $select.on('change', function () {
                var values = ! $(this).val() ? [] : $(this).val();
                var showToAllIndex = values.indexOf(allOptionId);

                if (-1 !== initialShowToAllIndex) {
                    if (-1 !== showToAllIndex && values.length > 1) {
                        for (var i = 0; i < values.length; i++) {
                            values.splice(showToAllIndex, 1);
                        }

                        $select.val(values);
                    }
                } else {
                    if (-1 !== showToAllIndex && values.length > 1) {
                        $select.val([allOptionId]);
                    }
                }

                if (! $(this).val()) {
                    $(this).val([allOptionId]);
                }

                initialValues = $(this).val();
                initialShowToAllIndex = initialValues.indexOf(allOptionId);
                self.syncChosen($select);
            });
        },

        /**
         * @param $select
         */
        syncChosen: function ($select) {
            $select.trigger("chosen:updated");
        },

        /**
         * @param $select
         */
        initUseDefaultListener: function ($select) {
            var $checkbox = $('input[name="use_default[geo_targeting]"]');

            if ($checkbox.length) {
                $checkbox.on('change', function () {
                    $select.trigger("chosen:updated");
                })
            }
        },
    });
});
