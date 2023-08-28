define(
    [
        'ko',
        'Magento_Payment/js/view/payment/cc-form'
    ],
    function (ko, Component) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Brasilcash_Gateway/payment/form',
                cpf: '',
                card_id: '',
                paymentMethod: ''
            },

            initObservable: function () {
                this._super()
                    .observe([
                        'cpf',
                        'card_id',
                        'paymentMethod'
                    ]);
                return this;
            },

            initialize: function () {
                this._super();
                this.paymentMethod(this.hasOpenbanking() ? '' : 'credit_card');
                this.isCreditCard = ko.computed(() => {
                    return this.paymentMethod() === 'credit_card';
                });
                this.isPix = ko.computed(() => {
                    return this.paymentMethod() === 'pix';
                });
                this.renderCardForm = ko.computed(() => {
                    return !this.card_id() && this.isCreditCard();
                });
            },

            getCode: function () {
                return 'brasilcashPaymentGateway';
            },

            isActive: function () {
                return true;
            },

            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'cc_number': this.creditCardNumber(),
                        'cc_exp_month': this.creditCardExpMonth(),
                        'cc_exp_year': this.creditCardExpYear(),
                        'cc_cid': this.creditCardVerificationNumber(),
                        'cpf': this.cpf(),
                        'card_id': this.card_id(),
                        'payment_method': this.paymentMethod(),
                    }
                };
            },

            getCards: function () {
                return window.checkoutConfig.payment.cards || [];
            },

            hasCards: function () {
                return this.getCards().length > 0;
            },

            hasOpenbanking: function () {
                return window.checkoutConfig.payment.openbanking;
            },

            validate: function () {
                if (!this.paymentMethod()) {
                    alert('Informe o método de pagamento.');
                    return false;
                }
                if (!this.cpf()) {
                    alert('O CPF deve ser informado.');
                    return false;
                }
                if (this.cpf().length < 11) {
                    alert('Informe o CPF completo, apenas números.');
                    return false;
                }
                if (this.paymentMethod() !== 'credit_card') {
                    return true;
                }
                if (!this.card_id() && !this.creditCardNumber()) {
                    alert('Informe o número do cartão de cŕedito.');
                    return false;
                }
                if (!this.card_id() && !this.creditCardExpMonth()) {
                    alert('Informe o mês de validade do cartão de cŕedito.');
                    return false;
                }
                if (!this.card_id() && !this.creditCardExpYear()) {
                    alert('Informe o ano de validade do cartão de cŕedito.');
                    return false;
                }
                if (!this.card_id() && !this.creditCardVerificationNumber()) {
                    alert('Informe o CVV do cartão de cŕedito.');
                    return false;
                }
                return true;
            }
        });
    }
);