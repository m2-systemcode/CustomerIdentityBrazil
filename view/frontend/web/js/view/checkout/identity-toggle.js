define([
    'jquery',
    'uiComponent',
    'uiRegistry',
    'SystemCode_CustomerIdentityBrazil/js/model/checkout-identity',
    'mage/translate'
], function ($, Component, registry, checkoutIdentity, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'SystemCode_CustomerIdentityBrazil/checkout/identity-toggle'
        },

        initialize: function () {
            this._super();
            this.checkoutIdentityConfig = window.checkoutConfig.customerIdentityBrazil || { isActive: false };

            if (!this.checkoutIdentityConfig.isActive) {
                return this;
            }

            checkoutIdentity.init(this.checkoutIdentityConfig);
            this.personType = checkoutIdentity.getPersonType();
            this.personType.subscribe(this.onPersonTypeChange, this);
            this.onPersonTypeChange(this.personType());

            return this;
        },

        onPersonTypeChange: function (personType) {
            this.syncPersonTypeToProvider(personType);
            this.updateNameLabels(personType);
        },

        syncPersonTypeToProvider: function (personType) {
            registry.async('checkoutProvider')(function (provider) {
                provider.set('brazilIdentity.person_type', personType || 'cpf');
            });
        },

        updateNameLabels: function (personType) {
            if (this.checkoutIdentityConfig.changeFirstnameLabel) {
                var firstnameLabel = personType === 'cnpj'
                    ? $t('Social Name')
                    : $t('First Name');

                $('.fieldset.address .field[name="shippingAddress.firstname"] .label span, ' +
                  '#shipping-new-address-form .field[name="shippingAddress.firstname"] .label span, ' +
                  '.fieldset.address .field._required:has(input[name="firstname"]) .label span').first()
                    .text(firstnameLabel);
            }

            if (this.checkoutIdentityConfig.changeLastnameLabel) {
                var lastnameLabel = personType === 'cnpj'
                    ? $t('Trade Name')
                    : $t('Last Name');

                $('.fieldset.address .field[name="shippingAddress.lastname"] .label span, ' +
                  '#shipping-new-address-form .field[name="shippingAddress.lastname"] .label span, ' +
                  '.fieldset.address .field._required:has(input[name="lastname"]) .label span').first()
                    .text(lastnameLabel);
            }
        },

        isToggleVisible: function () {
            return !!this.checkoutIdentityConfig.showToggle;
        }
    });
});
