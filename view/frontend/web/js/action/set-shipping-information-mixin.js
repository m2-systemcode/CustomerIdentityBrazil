define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Customer/js/model/customer',
    'SystemCode_CustomerIdentityBrazil/js/model/checkout-identity',
    'SystemCode_CustomerIdentityBrazil/js/model/checkout-identity-sync',
    'SystemCode_CustomerIdentityBrazil/js/model/checkout-identity-persist'
], function ($, wrapper, customer, checkoutIdentity, checkoutIdentitySync, checkoutIdentityPersist) {
    'use strict';

    function shouldCollectIdentityAtCheckout(config) {
        return config.isActive
            && config.showIdentityFields !== false
            && !config.useAccountIdentity
            && !customer.isLoggedIn();
    }

    return function (setShippingInformationAction) {
        return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            var config = window.checkoutConfig.customerIdentityBrazil || {};

            if (!config.isActive) {
                return originalAction();
            }

            checkoutIdentity.init(config);

            if (shouldCollectIdentityAtCheckout(config)) {
                checkoutIdentitySync.syncFromProvider();

                try {
                    checkoutIdentity.validate(config);
                } catch (error) {
                    return $.Deferred().reject(error);
                }
            }

            return checkoutIdentityPersist.save().then(function () {
                return originalAction();
            });
        });
    };
});
