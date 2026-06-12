define([
    'ko',
    'uiComponent',
    'SystemCode_CustomerIdentityBrazil/js/model/checkout-identity',
    'SystemCode_CustomerIdentityBrazil/js/mask-helper',
    'SystemCode_CustomerIdentityBrazil/js/validation/cpf',
    'SystemCode_CustomerIdentityBrazil/js/validation/cnpj',
    'mage/translate'
], function (ko, Component, checkoutIdentity, maskHelper, cpfValidator, cnpjValidator, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'SystemCode_CustomerIdentityBrazil/checkout/identity-taxvat'
        },

        initialize: function () {
            this._super();
            this.checkoutIdentityConfig = window.checkoutConfig.customerIdentityBrazil || { isActive: false };

            if (!this.checkoutIdentityConfig.isActive) {
                return this;
            }

            checkoutIdentity.init(this.checkoutIdentityConfig);

            this.personType = checkoutIdentity.getPersonType();
            this.taxvat = ko.observable('');
            checkoutIdentity.bindField('taxvat', this.taxvat);

            this.showTaxvat = ko.pureComputed(function () {
                if (!this.checkoutIdentityConfig.isActive) {
                    return false;
                }

                return this.personType() === 'cnpj'
                    ? !!this.checkoutIdentityConfig.taxvatCorporation
                    : !!this.checkoutIdentityConfig.taxvatIndividual;
            }, this);

            this.isTaxvatRequired = ko.pureComputed(function () {
                return this.personType() === 'cnpj'
                    ? !!this.checkoutIdentityConfig.taxvatCorporationRequired
                    : !!this.checkoutIdentityConfig.taxvatIndividualRequired;
            }, this);

            this.taxvatLabel = ko.pureComputed(function () {
                return this.personType() === 'cnpj' ? $t('CNPJ') : $t('CPF');
            }, this);

            this.taxvatFieldId = ko.pureComputed(function () {
                return this.personType() === 'cnpj' ? 'checkout-taxvat-cnpj' : 'checkout-taxvat-cpf';
            }, this);

            this.taxvatValidationCss = ko.pureComputed(function () {
                var css = {};

                if (this.isTaxvatRequired()) {
                    css['required-entry'] = true;
                }

                css[this.personType() === 'cnpj' ? 'validate-cnpj' : 'validate-cpf'] = true;

                return css;
            }, this);

            this.personType.subscribe(this.applyTaxvatMask, this);
            this.applyTaxvatMask();

            return this;
        },

        onTaxvatRender: function (element) {
            var maskType = this.personType() === 'cnpj' ? 'cnpj' : 'cpf';

            maskHelper.apply(element, maskType);
        },

        applyTaxvatMask: function () {
            var maskType = this.personType() === 'cnpj' ? 'cnpj' : 'cpf',
                fieldId = maskType === 'cnpj' ? '#checkout-taxvat-cnpj' : '#checkout-taxvat-cpf';

            maskHelper.applyWhenReady(fieldId, maskType);
        }
    });
});
