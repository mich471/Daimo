define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'jquery_cpfcnpj',
        'jquery_mask',
        'domReady!'
    ],
    function ($, Component, jquery_cpfcnpj, jquery_mask) {
        'use strict';
        $(document).ready( function() {
            $('#foxsea_paghiper_taxvat').val('');
            $('#foxsea_paghiper_taxvat').mask('00.000.000/0000-00', {reverse: true});

            //$('#foxsea_paghiper_place_order').prop('disabled', true);
            $('#foxsea_paghiper_place_order').attr("disabled", true);
            console.log("DOM Ready!");

            $('input#foxsea_paghiper_taxvat').on('keyup', function() {
                console.log("\n\n\n\n\n\nINVOKE BEGIN");
                var validationSelected = $('#foxsea_paghiper_select_cpfcnpj').find(":selected").val();
                var inputValue = $('input#foxsea_paghiper_taxvat').val();

                console.log("\n\n\nValues:\t" + validationSelected + "\t" + inputValue);

                if (validationSelected == 'cpf') {
                    console.log("\n\n\nSelected to validate CPF");
                    if (inputValue.length < 14) {
                        console.log("Case length is less than required");
                        $('input#foxsea_paghiper_taxvat').addClass("foxsea_paghiper_taxvat_invalid");
                        $('#foxsea_paghiper_place_order').prop('disabled', true);
                    }
                    if (inputValue.length == 14) {
                        console.log("Case length is equal to required");
                        $('input#foxsea_paghiper_taxvat').removeClass("foxsea_paghiper_taxvat_invalid");
                        $('#foxsea_paghiper_place_order').prop('disabled', false);
                    }
                }
                if (validationSelected == 'cnpj') {
                    console.log("\n\n\nSelected to validate CNPJ. Length: " + inputValue.length);
                    if (inputValue.length < 18) {
                        console.log("Case length is less than required");
                        $('input#foxsea_paghiper_taxvat').addClass("foxsea_paghiper_taxvat_invalid");
                        $('#foxsea_paghiper_place_order').prop('disabled', true);
                    }
                    if (inputValue.length == 18) {
                        console.log("Case length is equal to required");
                        $('input#foxsea_paghiper_taxvat').removeClass("foxsea_paghiper_taxvat_invalid");
                        $('#foxsea_paghiper_place_order').prop('disabled', false);
                    }
                }
                //console.log("keyup");
                console.log("INVOKE END\n\n\n\n\n\n");
            });
        });
        return Component.extend({
            defaults: {
                template: 'Foxsea_Paghiper/payment/paghiper',
                paghiper_taxvat: '',
            },
            initObservable: function () {
                this._super()
                .observe([
                    'paghiper_taxvat',
                    ]);

                return this;
            },
            context: function() {
                return this;
            },
            getCode: function() {
                return 'foxsea_paghiper';
            },
            isActive: function() {
                console.log("dasdasda");
                return true;
            },
            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'paghiper_taxvat': $('input#' + this.getCode() + '_taxvat').val()
                    }
                }
            },
            optionChanged: function(obj, event){
                var type = event.target.value;
                var masks = ['00.000.000/0000-00', '000.000.000-00'];
                var mask = type.length === 0 || type.length >= 12 ? masks[1] : masks[0];
                    if (type === 'cnpj') {
                        $('#foxsea_paghiper_taxvat').val('');
                        $('#foxsea_paghiper_taxvat').mask('00.000.000/0000-00', {reverse: true});
                }
                if (type === 'cpf') {
                    $('#foxsea_paghiper_taxvat').val('');
                    $('#foxsea_paghiper_taxvat').mask('000.000.000-00', {reverse: true});
                }
            },
            inputChanged: function(obj, event){
                var type = event.target.value;
                console.log(type);

                console.log("Input changed");
                console.log("\n\n\n\n\n\nINVOKE BEGIN");
                var validationSelected = $('#foxsea_paghiper_select_cpfcnpj').find(":selected").val();
                var inputValue = $('input#foxsea_paghiper_taxvat').val();

                console.log("\n\n\nValues:\t" + validationSelected + "\t" + inputValue);

                if (validationSelected == 'cpf') {
                    console.log("\n\n\nSelected to validate CPF");
                    if (inputValue.length < 14) {
                        console.log("Case length is less than required");
                        $('input#foxsea_paghiper_taxvat').addClass("foxsea_paghiper_taxvat_invalid");
                        $('#foxsea_paghiper_place_order').prop('disabled', true);
                    }
                    if (inputValue.length == 14) {
                        console.log("Case length is equal to required");
                        $('input#foxsea_paghiper_taxvat').removeClass("foxsea_paghiper_taxvat_invalid");
                        $('#foxsea_paghiper_place_order').prop('disabled', false);
                        this.isPlaceOrderActionAllowed(true);
                    }
                }
                if (validationSelected == 'cnpj') {
                    console.log("\n\n\nSelected to validate CNPJ. Length: " + inputValue.length);
                    if (inputValue.length < 18) {
                        console.log("Case length is less than required");
                        $('input#foxsea_paghiper_taxvat').addClass("foxsea_paghiper_taxvat_invalid");
                        $('#foxsea_paghiper_place_order').prop('disabled', true);
                    }
                    if (inputValue.length == 18) {
                        console.log("Case length is equal to required");
                        $('input#foxsea_paghiper_taxvat').removeClass("foxsea_paghiper_taxvat_invalid");
                        $('#foxsea_paghiper_place_order').prop('disabled', false);
                        this.isPlaceOrderActionAllowed(true);
                    }
                }
                //console.log("keyup");
                console.log("INVOKE END\n\n\n\n\n\n");
            },
            initialize: function () {
                this._super();
                // Disabled in initially
                this.isPlaceOrderActionAllowed(false);
            },
        });
    }
);
