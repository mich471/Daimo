define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal'
    ],
    function ($) {
        "use strict";
        var dataForm = $('#modal-form');
        dataForm.mage('validation', {});


        //creating jquery widget
        $.widget('Vendor2.modalForm2', {
            options2: {
                modalForm2: '#modal-form-confirm-delivery',
                modalButton2: '.open-modal-form-confirm-delivery',
            },
            _create: function () {
                this.options2.modalOption2 = this._getModalOptions2();
                this._bind();
            },
            _getModalOptions2: function () {
                /**
                 * Modal options
                 */
                let options2 = {
                    type: 'popup',
                    responsive: true,
                    title: 'Confirmar recebimento',
                    buttons: [
                        {
                            text: $.mage.__('Tudo certo, quero confirmar agora'),
                            class: 'btnNext action primary',
                            click: function () {
                                // this.closeModal();
                                if ($('.display-one').css('display', 'block')) {
                                    $('.display-one').css('display', 'none');
                                    $('.display-two').css('display', 'block');
                                    $('.btnToEvaluate').css('display', 'inline');
                                    $('.btnNext').css('display', 'none');
                                }
                            }
                        },
                        {
                            text: $.mage.__('Avaliar'),
                            class: 'btnToEvaluate action primary',
                            click: function () {
                                this.closeModal();
                                location.reload();
                                if ($('.display-two').css('display', 'block')) {
                                    $('.open-modal-form-confirm-delivery').css('display', 'none');
                                }
                            }
                        },

                        {
                            text: $.mage.__('Depois'),
                            class: 'btnClose-confirm-delivery action primary',
                            click: function () {
                                $('.open-modal-form-evaluate-order').css('display', 'none');
                                this.closeModal();
                            }
                        }]
                };
                return options2;
            },
            _bind: function () {
                var modalOption2 = this.options2.modalOption2;
                var modalForm2 = this.options2.modalForm2;


                $(document).on('click', this.options2.modalButton2, function () {
                    //Initialize modal
                    $(modalForm2).modal(modalOption2);
                    //open modal
                    $(modalForm2).trigger('openModal');
                });
            },
        });
        return $.Vendor2.modalForm2;
    }
);
