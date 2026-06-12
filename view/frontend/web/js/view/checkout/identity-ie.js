define([
    'ko',
    'uiComponent',
    'SystemCode_CustomerIdentityBrazil/js/model/checkout-identity'
], function (ko, Component, checkoutIdentity) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'SystemCode_CustomerIdentityBrazil/checkout/identity-ie'
        },

        initialize: function () {
            this._super();
            this.checkoutIdentityConfig = window.checkoutConfig.customerIdentityBrazil || { isActive: false };

            if (!this.checkoutIdentityConfig.isActive) {
                return this;
            }

            checkoutIdentity.init(this.checkoutIdentityConfig);

            this.personType = checkoutIdentity.getPersonType();
            this.ie = ko.observable('');
            checkoutIdentity.bindField('ie', this.ie);

            this.showIe = ko.pureComputed(function () {
                return !!this.checkoutIdentityConfig.ieVisible && this.personType() === 'cnpj';
            }, this);

            this.isIeRequired = ko.pureComputed(function () {
                return !!this.checkoutIdentityConfig.ieRequired && this.personType() === 'cnpj';
            }, this);

            return this;
        }
    });
});
