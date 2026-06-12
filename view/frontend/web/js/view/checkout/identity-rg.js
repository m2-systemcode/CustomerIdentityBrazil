define([
    'ko',
    'uiComponent',
    'SystemCode_CustomerIdentityBrazil/js/model/checkout-identity'
], function (ko, Component, checkoutIdentity) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'SystemCode_CustomerIdentityBrazil/checkout/identity-rg'
        },

        initialize: function () {
            this._super();
            this.checkoutIdentityConfig = window.checkoutConfig.customerIdentityBrazil || { isActive: false };

            if (!this.checkoutIdentityConfig.isActive) {
                return this;
            }

            checkoutIdentity.init(this.checkoutIdentityConfig);

            this.personType = checkoutIdentity.getPersonType();
            this.rg = ko.observable('');
            checkoutIdentity.bindField('rg', this.rg);

            this.showRg = ko.pureComputed(function () {
                return !!this.checkoutIdentityConfig.rgVisible && this.personType() === 'cpf';
            }, this);

            this.isRgRequired = ko.pureComputed(function () {
                return !!this.checkoutIdentityConfig.rgRequired && this.personType() === 'cpf';
            }, this);

            return this;
        }
    });
});
