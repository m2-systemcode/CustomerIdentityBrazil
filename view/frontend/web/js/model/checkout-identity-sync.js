define([
    'jquery',
    'uiRegistry',
    'SystemCode_CustomerIdentityBrazil/js/model/checkout-identity'
], function ($, registry, checkoutIdentity) {
    'use strict';

    function syncFromProviderData(identity) {
        var config = window.checkoutConfig.customerIdentityBrazil || {},
            fields = ['person_type', 'cpf', 'cnpj', 'rg', 'ie', 'socialname', 'tradename', 'taxvat'],
            useAccountIdentity = config.showIdentityFields === false || config.useAccountIdentity;

        fields.forEach(function (field) {
            if (identity[field] === undefined) {
                return;
            }

            if (useAccountIdentity && !identity[field]) {
                return;
            }

            checkoutIdentity.set(field, identity[field] || '');
        });
    }

    function syncIdentityFromProvider(provider) {
        var identity = provider.get('brazilIdentity') || {};

        syncFromProviderData(identity);

        if (identity.person_type) {
            checkoutIdentity.set('person_type', identity.person_type);
        }
    }

    return {
        syncFromDom: function () {
            var config = window.checkoutConfig.customerIdentityBrazil || {};

            if (!config.isActive) {
                return;
            }

            registry.async('checkoutProvider')(function (provider) {
                syncIdentityFromProvider(provider);
            });
        },

        syncFromProvider: function () {
            var config = window.checkoutConfig.customerIdentityBrazil || {},
                provider = registry.get('checkoutProvider');

            if (!config.isActive || config.showIdentityFields === false || config.useAccountIdentity) {
                return;
            }

            if (provider) {
                syncIdentityFromProvider(provider);
            }
        },

        syncFromProviderData: syncFromProviderData
    };
});
