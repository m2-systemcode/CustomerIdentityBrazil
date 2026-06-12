define([
    './identity-field',
    'SystemCode_CustomerIdentityBrazil/js/mask-helper',
    'SystemCode_CustomerIdentityBrazil/js/model/checkout-identity',
    'mage/translate'
], function (IdentityField, maskHelper, checkoutIdentity, $t) {
    'use strict';

    return IdentityField.extend({
        defaults: {
            fieldCode: 'taxvat',
            personTypes: ['cpf', 'cnpj']
        },

        initObservable: function () {
            this._super().observe('label');

            return this;
        },

        initialize: function () {
            this._super();

            if (!this.checkoutIdentityConfig.isActive) {
                return this;
            }

            checkoutIdentity.getPersonType().subscribe(this.onPersonTypeChange, this);
            this.onPersonTypeChange();

            return this;
        },

        onPersonTypeChange: function () {
            this.maskApplied = false;
            this.updateTaxvatRules();
        },

        updateTaxvatRules: function () {
            var type = checkoutIdentity.getPersonType()(),
                isCnpj = type === 'cnpj';

            this.validation = this.validation || {};
            this.validation['validate-cpf'] = !isCnpj;
            this.validation['validate-cnpj'] = isCnpj;
            this.label(isCnpj ? $t('CNPJ') : $t('CPF'));
            this.updateVisibilityAndRequired();
        },

        isConfiguredVisible: function () {
            var config = this.checkoutIdentityConfig,
                type = checkoutIdentity.getPersonType()();

            return type === 'cnpj'
                ? !!config.taxvatCorporation
                : !!config.taxvatIndividual;
        },

        applyMask: function (element) {
            var maskType = checkoutIdentity.getPersonType()() === 'cnpj' ? 'cnpj' : 'cpf';

            if (this.maskApplied && this.currentMaskType === maskType) {
                return;
            }

            maskHelper.apply(element, maskType);
            this.maskApplied = true;
            this.currentMaskType = maskType;
        }
    });
});
