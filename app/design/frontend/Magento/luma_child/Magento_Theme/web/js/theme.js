/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/smart-keyboard-handler',
    'mage/mage',
    'slickCarousel',
    'mage/ie-class-fixer',
    'domReady!'
], function ($, keyboardHandler) {
    'use strict';

    if ($('body').hasClass('checkout-cart-index')) {
        if ($('#co-shipping-method-form .fieldset.rates').length > 0 &&
            $('#co-shipping-method-form .fieldset.rates :checked').length === 0
        ) {
            $('#block-shipping').on('collapsiblecreate', function () {
                $('#block-shipping').collapsible('forceActivate');
            });
        }
    }

    $('.cart-summary').mage('sticky', {
        container: '#maincontent'
    });


    $('.header.content > .header.links').clone().appendTo('#store\\.links');

    jQuery('.home-slide').slick({
        infinite: true,
        autoplay: true,
        autoplaySpeed: 5000,
        centerMode: false,
        centerPadding: '0px',
        arrows: true,
        dots: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        pauseOnFocus: false,
        pauseOnHover: false,

        prevArrow: '<span type="button" class="slick-prev"><span></span></span>',
        nextArrow: '<span type="button" class="slick-next"><span></span></span>',
        responsive: [

            {
              breakpoint: 992,
              settings: {
                slidesToShow: 1
              }
            },
            {
              breakpoint: 768,
              settings: {
                slidesToShow: 1,
                arrows: false
              }
            },
            {
              breakpoint: 640,
              settings: {
                slidesToShow: 1,
                arrows: false
              }
            }
        ]

    });

    jQuery('.home-slide').on('touchstart', e => {
        jQuery('.home-slide').slick('slickPlay');
    });

    keyboardHandler.apply();
});
