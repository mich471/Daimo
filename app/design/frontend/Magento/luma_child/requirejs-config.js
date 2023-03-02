/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    paths: {
        "slickCarousel": 'js/slick.min',
    },
    shim:{
        'slickCarousel':{
            'deps':['jquery']
        }
    },
    deps: [
        "js/review",
        "js/wordLimits"
    ]
};
