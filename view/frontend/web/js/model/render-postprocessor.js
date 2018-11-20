define(
    [
        'Magento_Ui/js/lib/view/utils/async',
        'underscore',
        'mageUtils',
        'Aheadworks_OneStepCheckout/js/model/render-postprocessor/float-label/converters-pool'
    ],
    function ($, _, utils, floatLabelConvertersPool) {
        'use strict';

        return {
            paymentMethodItemsSelectors: '[data-role=payment-methods-load] div.payment-method',
            actionsToolbarSelector: 'div.actions-toolbar',

            /**
             * Init post processing
             */
            initProcessing: function () {
                var self = this;

                $.async(this.paymentMethodItemsSelectors, function (methodItem) {
                    self._processPaymentMethodContent($(methodItem));
                });
            },

            /**
             * Process payment method item content
             *
             * @param {jQuery} element
             */
            _processPaymentMethodContent: function (element) {
                var methodCode = this._getPaymentMethodCode(element);

                this._hideActionToolbar(element);
                this._correctHtmlAttributes(element);
                this._correctHtml(element, methodCode);
                floatLabelConvertersPool.getConverter(methodCode).convertPaymentMethodInputs(element);
            },

            _correctHtml: function (element, methodCode) {
                var self = this;
                if(element.hasClass('_active')) {
                    $('.payment_title_block').addClass('active').attr('data-code',methodCode);
                    element.find('.payment-method-title.field.choice').addClass('active');
                    if(methodCode == 'paypal_express') {
                        this._hideActionToolbarOrder();
                    }
                }
                var html;
                if(methodCode == 'cardinity') {
                    html = '<img class="payment-icon" src="'+ window.checkoutConfig.cardinityImg +'" alt="Cardinity">';
                    element.find('.payment-method-title.field.choice label').append(html);
                }

                element.attr('data-code',methodCode);
                element.find('.payment-method-title.field.choice').attr('data-code',methodCode).appendTo('.payment_title_block');
            },

            _hideActionToolbarOrder: function () {
                $('li.toolbar_order').hide();
            },

            /**
             * Hide action toolbar
             *
             * @param {jQuery} methodItem
             */
            _hideActionToolbar: function (methodItem) {
                methodItem.find(this.actionsToolbarSelector).hide();
            },

            /**
             * Retrieve payment method code from method item element
             *
             * @param {jQuery} element
             * @returns {string}
             */
            _getPaymentMethodCode: function (element) {
                var input = element.find('input[name="payment[method]"]');

                return input.val();
            },

            /**
             * Check and correct html attributes
             *
             * @param {jQuery} element
             */
            _correctHtmlAttributes: function (element) {
                this._correctCheckboxFieldsAttributes(element);
            },

            /**
             * Correct checkboxes html attributes.
             * Searches for missed id or for attribute and set it
             *
             * @param {jQuery} element
             */
            _correctCheckboxFieldsAttributes: function (element) {
                element.find('.field.choice').each(function () {
                    var field = $(this),
                        input = field.find('input[type=checkbox]'),
                        label = field.find('label'),
                        forAttr,
                        idAttr;

                    if (input.length > 0 && label.length > 0) {
                        forAttr = label.attr('for');
                        idAttr = input.attr('id');

                        if (_.isUndefined(forAttr)) {
                            if (_.isUndefined(idAttr)) {
                                idAttr = utils.uniqueid();
                            }
                            label.attr('for', idAttr);
                        } else if (_.isUndefined(idAttr) || forAttr != idAttr) {
                            input.attr('id', forAttr);
                        }
                    }
                });
            }
        };
    }
);
