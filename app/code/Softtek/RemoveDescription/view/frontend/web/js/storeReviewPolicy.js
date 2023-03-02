define([
    'uiElement',
    'jquery'
], function(Component, $) {
    'use strict';
    return Component.extend({
        initialize: function() {
           $(".content-policy").appendTo('.pts-container_right')
        },
    });
});
