define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/url-builder',
    'mage/storage',
    'SystemCode_CustomerIdentityBrazil/js/model/checkout-identity',
    'SystemCode_CustomerIdentityBrazil/js/model/checkout-identity-sync'
], function ($, quote, customer, urlBuilder, storage, checkoutIdentity, checkoutIdentitySync) {
    'use strict';

    return {
        save: function () {
            var config = window.checkoutConfig.customerIdentityBrazil || {},
                deferred = $.Deferred();

            if (!config.isActive) {
                deferred.resolve();
                return deferred.promise();
            }

            checkoutIdentity.init(config);

            if (!customer.isLoggedIn() && config.showIdentityFields !== false && !config.useAccountIdentity) {
                checkoutIdentitySync.syncFromProvider();
            }

            var data = checkoutIdentity.getData(),
                serviceUrl = customer.isLoggedIn()
                    ? urlBuilder.createUrl('/carts/mine/brazil-identity', {})
                    : urlBuilder.createUrl('/guest-carts/:cartId/brazil-identity', {
                        cartId: quote.getQuoteId()
                    });

            storage.post(serviceUrl, JSON.stringify({
                identity: data
            })).done(function () {
                deferred.resolve();
            }).fail(function () {
                deferred.reject();
            });

            return deferred.promise();
        }
    };
});
