require(['jquery',
'Magento_Customer/js/customer-data'], function ($, customerData) {

    $(document).ready(function(){
        function createCookie(name, value, days) {
            var expires;
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toGMTString();
            } else {
                expires = "";
            }
            document.cookie = escape(name) + "=" + escape(value) + expires + "; path=/";
        }

        function delete_cookie(name) {
            document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
        }

        function getCookie(name) {
            function escape(s) {
                return s.replace(/([.*+?\^$(){}|\[\]\/\\])/g, '\\$1');
            }
            var match = document.cookie.match(RegExp('(?:^|;\\s*)' + escape(name) + '=([^;]*)'));
            return match ? match[1] : null;
        }
        //let customer =customerData.get('customer');
    // if(document.getElementsByClassName("checkout-container").length <= 0){
    //     if(getCookie('seller')!==null || getCookie('seller')!==""){
    //         document.getElementById("search").className += " "+getCookie('seller');
    //     }
    //     if(getCookie('seller')!==null || getCookie('seller')!==""){
    //         if(document.getElementsByClassName("seller").length > 0) {
    //             for(var i = 0 ; i<document.getElementsByClassName("nav").length; i++){
    //                 if(document.getElementsByClassName("nav")[i].innerText == "Meus pedidos"){
    //                     document.getElementsByClassName("nav")[i].hidden = true
    //                 }
    //             }
    //         }
    //     }
    // }
    if(document.getElementsByClassName("fieldset create info").length > 0){
        var firstname = document.getElementById('firstname');
        var lastname = document.getElementById('lastname');
        let socialvalue = document.getElementById('socialvalue');
        let tradevalue = document.getElementById('tradevalue');
        let cnpj = document.getElementById('cnpj');
        let ie = document.getElementById('ie');
        var telephone = document.getElementById('telephone');
        var email_address = document.getElementById('email_address');

        document.getElementById("btn-regiter").addEventListener("click", function() {
            delete_cookie("firstname");
            delete_cookie("lastname");

            delete_cookie("socialvalue");
            delete_cookie("tradevalue");
            delete_cookie("cnpj");
            delete_cookie("ie");

            delete_cookie("telephone");
            delete_cookie("email_address");

            createCookie("firstname", firstname.value, "10");
            createCookie("lastname", lastname.value, "10");

            createCookie("socialvalue", socialvalue.value, "10");
            createCookie("tradevalue", tradevalue.value, "10");
            createCookie("cnpj", cnpj.value, "10");
            createCookie("ie", ie.value, "10");

            createCookie("telephone", telephone.value, "10");
            createCookie("email_address", email_address.value, "10");


        });

        if(getCookie('lastname')!==null || getCookie('lastname')!==""){
            document.getElementById("lastname").value = getCookie('lastname');
        }

        if(getCookie('firstname')!==null || getCookie('firstname')!==""){
            document.getElementById("firstname").value = getCookie('firstname');
        }

        if(getCookie('socialvalue')!==null || getCookie('socialvalue')!==""){
            document.getElementById("socialvalue").value = getCookie('socialvalue');
        }

        if(getCookie('tradevalue')!==null || getCookie('tradevalue')!==""){
            document.getElementById("tradevalue").value = getCookie('tradevalue');
        }

        if(getCookie('cnpj')!==null || getCookie('cnpj')!==""){
            document.getElementById("cnpj").value = getCookie('cnpj');
        }

        if(getCookie('ie')!==null || getCookie('ie')!==""){
            document.getElementById("ie").value = getCookie('ie');
        }

        if(getCookie('telephone')!==null || getCookie('telephone')!==""){
            document.getElementById("telephone").value = getCookie('telephone');
        }

        if(getCookie('email_address')!==null || getCookie('email_address')!==""){
            document.getElementById("email_address").value = getCookie('email_address');
        }
            function createCookie(name, value, days) {
                var expires;
                if (days) {
                    var date = new Date();
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                    expires = "; expires=" + date.toGMTString();
                } else {
                    expires = "";
                }
                document.cookie = escape(name) + "=" + escape(value) + expires + "; path=/";
            }

            function delete_cookie(name) {
                document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            }

            function getCookie(name) {
                function escape(s) {
                    return s.replace(/([.*+?\^$(){}|\[\]\/\\])/g, '\\$1');
                }
                var match = document.cookie.match(RegExp('(?:^|;\\s*)' + escape(name) + '=([^;]*)'));
                return match ? match[1] : null;
            }
        }

     });



    /* ***************** Validate Field only Alfbaeto ***********/
    $(".customer-account-create #firstname").on("keypress", function (e) {
        var charCode = event.keyCode;

        if ((charCode > 64 && charCode < 91) || (charCode > 96 && charCode < 123) || charCode == 8)

            return true;
        else
            return false;
    });

    $(".customer-account-create #lastname").on("keypress", function (e) {
        var charCode = event.keyCode;

        if ((charCode > 64 && charCode < 91) || (charCode > 96 && charCode < 123) || charCode == 8)

            return true;
        else
            return false;
    });
});
