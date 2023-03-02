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

require([
    'jquery',
    'mage/translate',
    'Plumrocket_CookieConsent/js/chosen.jquery.min',
    'domReady!'
], function ($, __) {
    'use strict';

    /* enable chosen */
    setTimeout(function () {
        /* Scope. */$('#pr_cookie_main_settings_geo_targeting_inherit:checked').click().click();

        initializeGeoIPSelects();
    }, 2000);

    /* fix for chosen in not expanded section */
    $('#pr_cookie_main_settings-head').on('click', function () {
        reinitializeGeoIPSelect(document.getElementById('pr_cookie_main_settings_geo_targeting'));
    });

    $('#pr_cookie_main_settings_geo_targeting_inherit').on('click', function () {
        reinitializeGeoIPSelect(document.getElementById('pr_cookie_main_settings_geo_targeting'));
    });

    /* Registry for all selects with class geoip-select-with-chosen */
    window.initializedSelects = [];

    function initializeGeoIPSelects()
    {
        var selects = getGeoIPSelects();

        selects.forEach(function (select) {
            if (! isInitializedGeoIPSelect(select)) {
                initializeGeoIPSelect(select);
                initializeGeoIPOptionDepends(select);

                if (!$(select).hasClass('prgdpr-coutry')) {
                    geoIpFieldsDepend($(select));

                    var id = $(select).attr('id');
                    $(select).parent().prepend('<div style="font-weight: 600; margin-bottom: 10px;" id="ccpa-html-wrap"></div>');
                    $('#ccpa-html-wrap').append($('[for="' + id + '"]'));
                }
            }
        });
    }

    function geoIpFieldsDepend(select)
    {
        var geoTargeting = $('.prgdpr-coutry'), values, toggle = function () {
            values = $(geoTargeting).val();
            if (values) {
                values.forEach(function (element) {
                    if (element === 'US') {
                        select.parent().parent().show();
                    } else {
                        select.parent().parent().hide();
                    }
                });
            } else {
                select.parent().parent().hide();
            }
        };

        toggle(); /* Auto states toggle when page loaded */

        /* If we change Geo Targeting field */
        geoTargeting.change(function () {
            toggle();
        });
    }

    function getGeoIPSelects()
    {
        var result = $('select.geoip-select-with-chosen');

        return result.length ? result.toArray() : [];
    }

    function initializeGeoIPSelect(select)
    {
        if (! select instanceof HTMLElement) {
            return false;
        }

        if (isInitializedGeoIPSelect(select)) {
            return getIndexOfRegisteredGeoIPSelect(select);
        }

        var enableSearch = true;
        if ("readonly" === select.getAttribute('readonly')) {
            enableSearch = false;
        }

        $(select).chosen({
            "display_selected_options" : true,
            "display_disabled_options": true,
            "hide_results_on_select": true,
            "group_search": enableSearch
        });

        return registerGeoIPSelect(select);
    }

    function registerGeoIPSelect(select)
    {
        return initializedSelects.push(select.name);
    }

    function getIndexOfRegisteredGeoIPSelect(select)
    {
        return initializedSelects.indexOf(select.name);
    }

    function isInitializedGeoIPSelect(select)
    {
        return -1 !== getIndexOfRegisteredGeoIPSelect(select);
    }

    function reinitializeGeoIPSelect(select)
    {
        if (isInitializedGeoIPSelect(select)) {
            $(select).chosen("destroy");
            initializedSelects.splice(getIndexOfRegisteredGeoIPSelect(select), 1);
            initializeGeoIPSelect(select);
        }
    }

    function updateGeoIpSelect(select)
    {
        if (! isInitializedGeoIPSelect(select)) {
            initializeGeoIPSelect(select);
        }

        $(select).trigger("chosen:updated");
    }

    function initializeGeoIPOptionDepends(select)
    {
        initializeGeoIPSelect(select);

        var geoipEl = $(select),
            allOptionId = 'all';

        var initialValues = ! geoipEl.val() ? [] : geoipEl.val();
        var initialShowToAllIndex = initialValues.indexOf(allOptionId);

        geoipEl.on('change', function () {
            var values = ! $(this).val() ? [] : $(this).val();
            var showToAllIndex = values.indexOf(allOptionId);

            if (-1 !== initialShowToAllIndex) {
                if (-1 !== showToAllIndex && values.length > 1) {
                    for (var i = 0; i < values.length; i++) {
                        values.splice(showToAllIndex, 1);
                    }

                    geoipEl.val(values);
                }
            } else {
                if (-1 !== showToAllIndex && values.length > 1) {
                    geoipEl.val([allOptionId]);
                }
            }

            if (! $(this).val()) {
                $(this).val([allOptionId]);
            }

            initialValues = $(this).val();
            initialShowToAllIndex = initialValues.indexOf(allOptionId);
            updateGeoIpSelect(select);
        });
    }
});
