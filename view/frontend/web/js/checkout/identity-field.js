define([
    'Magento_Ui/js/form/element/abstract',
    'SystemCode_CustomerIdentityBrazil/js/model/checkout-identity'
], function (Abstract, checkoutIdentity) {
    'use strict';

    return Abstract.extend({
        defaults: {
            fieldCode: '',
            personTypes: [],
            maskApplied: false,
            elementTmpl: 'SystemCode_CustomerIdentityBrazil/checkout/form/element/identity-input'
        },

        initialize: function () {
            this.checkoutIdentityConfig = window.checkoutConfig.customerIdentityBrazil || { isActive: false };

            if (this.checkoutIdentityConfig.isActive) {
                checkoutIdentity.init(this.checkoutIdentityConfig);
            }

            this._super();

            if (!this.checkoutIdentityConfig.isActive) {
                return this;
            }

            this.syncToCheckoutIdentity(this.value());
            this.value.subscribe(this.syncToCheckoutIdentity, this);
            this.updateVisibilityAndRequired();
            checkoutIdentity.getPersonType().subscribe(this.updateVisibilityAndRequired, this);

            return this;
        },

        syncToCheckoutIdentity: function (value) {
            if (this.fieldCode) {
                checkoutIdentity.set(this.fieldCode, value || '');
            }
        },

        updateVisibilityAndRequired: function () {
            var type = checkoutIdentity.getPersonType()(),
                shouldShow = this.isConfiguredVisible() && this.personTypes.indexOf(type) !== -1,
                shouldRequire = shouldShow && this.isConfiguredRequired(type);

            this.visible(shouldShow);
            this.required(shouldRequire);
            this.validation = this.validation || {};
            this.validation['required-entry'] = shouldRequire;
        },

        isConfiguredVisible: function () {
            var config = this.checkoutIdentityConfig,
                code = this.fieldCode,
                flag = config[code + 'Visible'];

            if (flag !== undefined) {
                return !!flag;
            }

            return !!(config.fields && config.fields[code]);
        },

        isConfiguredRequired: function (type) {
            var config = this.checkoutIdentityConfig,
                code = this.fieldCode,
                flag = config[code + 'Required'];

            if (flag !== undefined) {
                return !!flag;
            }

            if (config.fields && config.fields[code]) {
                return !!config.fields[code].required;
            }

            if (code === 'taxvat') {
                return type === 'cnpj'
                    ? !!config.taxvatCorporationRequired
                    : !!config.taxvatIndividualRequired;
            }

            return false;
        },

        onElementRender: function (element) {
            this.applyMask(element);
        },

        applyMask: function () {}
    });
});
