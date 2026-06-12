define([
    'mage/utils/wrapper',
    'Magento_Customer/js/model/customer',
    'SystemCode_CustomerIdentityBrazil/js/model/checkout-identity',
    'SystemCode_CustomerIdentityBrazil/js/model/checkout-identity-sync',
    'SystemCode_CustomerIdentityBrazil/js/model/checkout-identity-persist'
], function (wrapper, customer, checkoutIdentity, checkoutIdentitySync, checkoutIdentityPersist) {
    'use strict';

    return function (placeOrderAction) {
        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, messageContainer) {
            var config = window.checkoutConfig.customerIdentityBrazil || {};

            if (!config.isActive) {
                return originalAction(paymentData, messageContainer);
            }

            checkoutIdentity.init(config);

            if (!customer.isLoggedIn() && config.showIdentityFields !== false && !config.useAccountIdentity) {
                checkoutIdentitySync.syncFromDom();
            }

            return checkoutIdentityPersist.save().then(function () {
                return originalAction(paymentData, messageContainer);
            });
        });
    };
});
