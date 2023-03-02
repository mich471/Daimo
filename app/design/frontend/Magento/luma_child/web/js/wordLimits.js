define([
        "jquery"
    ],
    function($) {
        "use strict";
        //LIMITES DE CARACTERES...
        $(".limited").keypress(function(event){
            var maxLength = 200;
            var length = this.value.length;
            if (length >= maxLength)
            {
                this.value = this.value.substring(0, maxLength);
                console.log(maxLength + ' characters allowed, excess characters trimmed');
            }
        });

        $(".limited").on('keyup',function(){
            jQuery('.charnum').remove();
            var maxLength = 200;
            var length = this.value.length;

            var count=maxLength-length;
            jQuery('<span class="charnum">' +count+' Caracteres</span>').insertAfter(jQuery(this)).css('padding-left' , '15px');

        });
    });
