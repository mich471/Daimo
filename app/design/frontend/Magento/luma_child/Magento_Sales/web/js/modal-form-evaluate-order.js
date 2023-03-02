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
        $.widget('Vendor.modalForm', {
            options: {
                modalForm: '#modal-form-evaluate-order',
                modalButton: '.open-modal-form-evaluate-order'
            },
            _create: function () {
                this.options.modalOption = this._getModalOptions();
                this._bind();
            },
            _getModalOptions: function () {
                /**
                 * Modal options
                 */
                let options = {
                    type: 'popup',
                    responsive: true,
                    title: 'Avaliação de pedido',
                    modalClass: 'order-evaluation-popup',
                    buttons: [{
                        text: $.mage.__(''),
                        class: 'btnClose-evaluate-order',
                        click: function () {
                            /* some stuff */
                            this.closeModal();
                        }
                    }]
                };

                return options;
            },
            _bind: function () {
                var modalOption = this.options.modalOption;
                var modalForm = this.options.modalForm;

                $(document).on('click', this.options.modalButton, function () {
                    //Initialize modal
                    $(modalForm).modal(modalOption);
                    //open modal
                    $(modalForm).trigger('openModal');
                    $('.order-evaluation-popup .modal-footer').hide();
                    $('.order-evaluation-popup h3').css('margin-bottom', '1rem');
                    $('.order-evaluation-popup .modal-content').css('overflow', 'hidden');
                    $('.sales-order-view .block-collapsible-nav').attr('style', 'z-index: 900 !important');
                });
            }
        });

        return $.Vendor.modalForm;

    }
);
