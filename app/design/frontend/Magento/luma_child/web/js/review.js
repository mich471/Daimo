define([
        "jquery"
    ],
    function($) {
        "use strict";
        //agrega reseña de la tienda debajo de la description
        $(".pts-container_review").appendTo(".pts-container_right");

        //muestra reseña de la tienda a darle click a el link
        $('.content-review-link').click(function(){
            $(".pts-container_review").toggle(200);
        });

        //ocultar una politica al darle click ala otra politica

        let content_policy_shipping = $(".content-policy-shipping");
        let content_policy_devolution = $(".content-policy-devolution");
        $('.policy-shipping').click(function () {
            $('.content-policy-shipping').toggle("swing",function () {
                if(content_policy_shipping.css('display') === 'block') {
                    $('.content-policy-devolution').css("display","none");
                }
            });
        });
        $('.policy-devolution').click(function () {
            $('.content-policy-devolution').toggle("swing",function () {
                if(content_policy_devolution.css('display') ===  'block' ){
                    $('.content-policy-shipping').css("display","none");
                }
            });
        });
    });

