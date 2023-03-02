/**
 * @copyright © Softtek. All rights reserved.
 * @author Paul Soberanes <paul.soberanes@softtek.com>
 * 2 Dias en esto
 */
 define(
    [
        'ko',
        'Magento_Payment/js/view/payment/cc-form',
        'jquery',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Payment/js/model/credit-card-validation/validator',
        'Softtek_Payment/js/view/payment/fingerprint2'
    ],
    function (ko, Component, $, placeOrder, additionalValidators, validator, fingerprint2) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Softtek_Payment/payment/softtek_payment'
            },
            getCode: function () {
                return 'softtek_payment';
            },
            isActive: function () {
                return true;
            },
            validate: function () {
                var $form = $('#' + this.getCode() + '-form');
                window.cambiada = 0
                //Cambiaron la tarjeta
                if (window.card_anterior === 0) {
                    window.card_anterior = document.getElementById('softtek_payment_cc_number').value
                } else {
                    window.card_actual = document.getElementById('softtek_payment_cc_number').value

                    if ((window.card_actual - window.card_anterior) !== 0) {
                        window.cambiada = true
                    }
                    window.card_anterior = window.card_actual
                }
                return $form.validation() && $form.validation('isValid'); //true pasa
            },
            initObservable: function () {
                this._super()
                    .observe([
                        'finger'
                    ]);
                return this;
            },
            /**
             * Get data
             * @returns {Object}
            */
            getData: function () {
                window.trys += 1
                let finger = document.getElementById('finger').value
                finger = finger.trim()
                let data = {
                    'method': this.item.method,
                    'additional_data': {
                        'cc_cid': this.creditCardVerificationNumber(),
                        'cc_ss_start_month': this.creditCardSsStartMonth(),
                        'cc_ss_start_year': this.creditCardSsStartYear(),
                        'cc_ss_issue': this.creditCardSsIssue(),
                        'cc_type': this.creditCardType(),
                        'cc_exp_year': this.creditCardExpYear(),
                        'cc_exp_month': this.creditCardExpMonth(),
                        'cc_number': this.creditCardNumber(),
                        'finger': finger,
                        'card_pasted': window.paste,
                        'trys': window.trys,
                        'card_changed': window.cambiada
                    }
                };
                document.getElementById("cybersource").src = window.urlfinger + finger;
                var script = document.createElement('script');
                script.type = 'text/javascript';
                script.src = window.urlfinger + finger;
                document.getElementsByTagName('head')[0].appendChild(script);
                return data;
            },
            getUrlCyber: function () {
                window.urlfinger = _.map(window.checkoutConfig.payment.calculadora.version, function (value, key) {
                    return {
                        'value': key,
                        'transaction_result': value
                    }
                })[0].transaction_result;
            },
            getFingerPrint: function () {
                var murmur;
                if (window.requestIdleCallback) {
                    var stCounter1 = 0;
                    var stLooper1 = setInterval(function() {
                        stCounter1++;
                        requestIdleCallback(function () {
                            fingerprint2.get(function (components) {
                                var values = components.map(function (component) {
                                    return component.value
                                })
                                murmur = fingerprint2.x64hash128(values.join(''), 31)
                                document.getElementById('finger').value = murmur;
                                document.getElementById("finger").disabled = true;
console.log('if true > ' + stCounter1 + ': ' + murmur);
                                clearInterval(stLooper1);
                                return murmur
                            })
                        });
                        if (stCounter1 >= 10) {
                            clearInterval(stLooper1);
                        }
                    }, 500);
                } else {
                    var stCounter2 = 0;
                    var stLooper2 = setInterval(function() {
                        stCounter2++;
                        fingerprint2.get(function (components) {
                            var values = components.map(function (component) { return component.value })
                            murmur = fingerprint2.x64hash128(values.join(''), 31)
                            document.getElementById('finger').value = murmur;
                            document.getElementById("finger").disabled = true;
                            clearInterval(stLooper2);
console.log('if false > ' + stCounter2 + ': ' + murmur);
                            return murmur
                        });
                        if (stCounter2 >= 10) {
                            clearInterval(stLooper2);
                        }
                    }, 500);
                }
            },
            paste: function () {
                window.trys = 0
                window.paste = 0
                if (window.requestIdleCallback) {
                    setInterval(function () {
                        let target = document.getElementById('softtek_payment_cc_number');
                        target.addEventListener('paste', (event) => {
                            let paste = (event.clipboardData || window.clipboardData).getData('text');
                            target.value = paste
                            window.paste = true
                            event.preventDefault();
                        });
                    }, 500)
                } else {
                    setInterval(function () {
                        let target = document.getElementById('softtek_payment_cc_number');
                        target.addEventListener('paste', (event) => {
                            let paste = (event.clipboardData || window.clipboardData).getData('text');
                            target.value = paste
                            window.paste = true
                            event.preventDefault();
                        });
                    }, 500)
                }
            },
            credit_card_change_event: function () {
                window.card_anterior = 0
                window.card_actual = 0
                window.cambiada = 0
            },
            hideCCV_CC: function () {
                setInterval(function () {
                    if (document.getElementById('softtek_payment_cc_cid') != undefined && document.getElementById('softtek_payment_cc_number') != undefined) {
                        document.getElementById('softtek_payment_cc_cid').type = 'password';
                    }
                }, 3000)
            },
            CardNumberHide: function(){

                /*setInterval(function () {
                    var value = $("#softtek_payment_cc_number").val();
                    if(value.length !== undefined) {

                        if (value.length <= 12) {
                            // 415231343436
                            $("span.dots").html(value.slice(0, 12));
                            $("span.normal").html("");
                        }
                        else {

                            $("span.normal").html(value.slice(12,18));
                        }
                    }
                }, 100)*/
            }
        });
    }
);
